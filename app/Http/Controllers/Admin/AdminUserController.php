<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Purchase;
use App\Models\ServiceRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AdminUserController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'admin']);
    }

    /**
     * Display a listing of users.
     */

public function index(Request $request)
{
    $query = User::query();

    // البحث
    if ($request->filled('search')) {
        $search = $request->search;
        $query->where(function ($q) use ($search) {
            $q->where('name', 'like', "%{$search}%")
              ->orWhere('email', 'like', "%{$search}%")
              ->orWhere('phone', 'like', "%{$search}%");
        });
    }

    // الفلترة بالتاريخ
    if ($request->filled('date_from')) {
        $query->where('created_at', '>=', $request->date_from);
    }
    if ($request->filled('date_to')) {
        $query->where('created_at', '<=', $request->date_to . ' 23:59:59');
    }

    $users = $query->withCount([
            'purchases' => function ($q) {
                $q->where('payment_status', 'completed');
            },
            'serviceRequests'
        ])
        ->orderBy('created_at', 'desc')
        ->paginate(20);

    // الإحصائيات البديلة
    $totalUsers           = User::where('role', 'user')->count();
    $recentUsers          = User::where('role', 'user')->where('created_at', '>=', now()->subDays(30))->count();
    $totalServiceRequests = ServiceRequest::count();
    $admins               = User::where('role', 'admin')->count();
    $totalUsers = User::where('role', 'user')->count();
$recentUsers = User::where('role', 'user')
                   ->whereMonth('created_at', now()->month)
                   ->count();
$totalServiceRequests = ServiceRequest::count();
$admins = User::where('role', 'admin')->count();

// مثال بسيط لحساب نسبة الاحتفاظ (يمكنك تعديل المعادلة حسب تعريفك)
if ($totalUsers > 0) {
    $retentionRate = (($totalUsers - $recentUsers) / $totalUsers) * 100;
} else {
    $retentionRate = 0;
}

return view('admin.users.index', compact(
    'users',
    'totalUsers',
    'recentUsers',
    'totalServiceRequests',
    'admins',
    'retentionRate'
));


    
}

    /**
     * Store a newly created user.
     */
    public function store(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'phone' => 'nullable|string|max:20',
            'password' => 'required|string|min:6|confirmed',
            'role' => 'required|in:admin,user',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }


        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
            'role' => $request->role,
        ]);

        return redirect()->route('admin.users.index')
            ->with('success', 'تم إنشاء المستخدم بنجاح!');
    }

    /**
     * Show the form for editing the specified user.
     */
    public function edit(User $user)
    {


        return view('admin.users.edit', compact('user'));
    }

    /**
     * Update the specified user.
     */
    public function update(Request $request, User $user)
    {



        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:20',
            'password' => 'nullable|string|min:6|confirmed',
            'role' => 'required|in:admin,user',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }


        $userData = [
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'role' => $request->role,
        ];

        if ($request->filled('password')) {
            $userData['password'] = Hash::make($request->password);
        }

        $user->update($userData);

        return redirect()->route('admin.users.show', $user)
            ->with('success', 'تم تحديث بيانات المستخدم بنجاح!');
    }

    /**
     * Remove the specified user.
     */
    public function destroy(User $user)
    {

        // Check if user has any completed purchases
        if ($user->purchases()->where('payment_status', 'completed')->exists()) {
            return back()->with('error', 'لا يمكن حذف المستخدم لأنه يحتوي على مشتريات مكتملة.');
        }

        // Delete user's data
        $user->serviceRequests()->delete();
        $user->reviews()->delete();
        $user->learningProgress()->delete();
        $user->purchases()->delete();
        
        $user->delete();

        return redirect()->route('admin.users.index')
            ->with('success', 'تم حذف المستخدم بنجاح!');
    }

    /**
     * Show user's purchases.
     */
    public function purchases(User $user)
    {

        $purchases = $user->purchases()
            ->with('course')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('admin.users.purchases', compact('user', 'purchases'));
    }

    /**
     * Show user's service requests.
     */
    public function serviceRequests(User $user)
    {

        $serviceRequests = $user->serviceRequests()
            ->with('serviceType')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('admin.users.service-requests', compact('user', 'serviceRequests'));
    }

    /**
     * Export users data.
     */
    public function export(Request $request)
    {
        $query = User::where('role', 'user');

        // Apply same filters as index
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        if ($request->filled('date_from')) {
            $query->where('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->where('created_at', '<=', $request->date_to . ' 23:59:59');
        }

        $users = $query->get();

        $csvData = "الاسم,البريد الإلكتروني,الهاتف,تاريخ التسجيل,عدد الدورات المشتراة,إجمالي المبلغ المدفوع\n";
        
        foreach ($users as $user) {
            $coursesCount = $user->purchases()->where('payment_status', 'completed')->count();
            $totalSpent = $user->purchases()->where('payment_status', 'completed')->sum('amount');
            
            $csvData .= sprintf(
                "%s,%s,%s,%s,%d,%.2f\n",
                $user->name,
                $user->email,
                $user->phone ?? '',
                $user->created_at->format('Y-m-d'),
                $coursesCount,
                $totalSpent
            );
        }

        $fileName = 'users_export_' . now()->format('Y_m_d_H_i_s') . '.csv';
        
        return response($csvData)
            ->header('Content-Type', 'text/csv; charset=UTF-8')
            ->header('Content-Disposition', 'attachment; filename="' . $fileName . '"')
            ->header('Content-Encoding', 'UTF-8');
    }
    public function show(User $user)
{

    return view('admin.users.show', compact('user'));
}
public function create()
{
    return view('admin.users.create');
}

}

