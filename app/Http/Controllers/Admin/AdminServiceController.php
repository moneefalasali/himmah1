<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ServiceRequest;
use App\Models\ServiceType;
use App\Models\Message;
use App\Models\MessageFile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class AdminServiceController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'admin']);
    }

    /**
     * Display a listing of service requests.
     */
public function index(Request $request)
{
    $query = ServiceRequest::with(['user', 'serviceType']);

    // ... نفس الفلاتر والبحث ...

    $serviceRequests = $query->orderBy('created_at', 'desc')->paginate(20);
    $serviceTypes = ServiceType::all();

    // إحصائيات عامة
    $totalRequests = ServiceRequest::count();
    $completedRequests = ServiceRequest::where('status', 'completed')->count();
    $urgentRequests = ServiceRequest::where('is_urgent', true)->count();
    $totalRevenue = ServiceRequest::sum('amount');

    // بيانات الرسم البياني - توزيع الطلبات حسب النوع
    $requestsByTypeData = ServiceRequest::selectRaw('service_type_id, COUNT(*) as count')
        ->groupBy('service_type_id')
        ->pluck('count', 'service_type_id');

    $requestsByType = [
        'labels' => [],
        'data' => [],
    ];

    foreach ($serviceTypes as $type) {
        $requestsByType['labels'][] = $type->name;
        $requestsByType['data'][] = $requestsByTypeData->get($type->id, 0);
    }

    // بيانات الرسم البياني - توزيع الطلبات حسب الحالة (status)
    $statusCounts = ServiceRequest::selectRaw('status, COUNT(*) as count')
        ->groupBy('status')
        ->pluck('count', 'status');

    // هنا تحدد ترتيب أو أسماء الحالات التي تريدها بالضبط، مثلاً:
    $statusLabels = ['completed', 'pending', 'in_progress', 'cancelled'];

    $requestsByStatus = [
        'labels' => [],
        'data' => [],
    ];

    foreach ($statusLabels as $status) {
        $requestsByStatus['labels'][] = ucfirst(str_replace('_', ' ', $status)); // صيغة عرض جميلة
        $requestsByStatus['data'][] = $statusCounts->get($status, 0);
    }

    return view('admin.services.index', compact(
        'serviceRequests',
        'serviceTypes',
        'totalRequests',
        'completedRequests',
        'urgentRequests',
        'totalRevenue',
        'requestsByType',
        'requestsByStatus' // أضفنا هذا أيضاً
    ));
}
    /**
     * Display the specified service request.
     */
    public function show(ServiceRequest $serviceRequest)
    {
        $serviceRequest->load([
            'user',
            'serviceType',
            'files',
            'messages.sender',
            'messages.files'
        ]);

        return view('admin.services.show', compact('serviceRequest'));
    }

    /**
     * Update the status of a service request.
     */
    public function updateStatus(Request $request, ServiceRequest $serviceRequest)
    {
        $validator = Validator::make($request->all(), [
            'status' => 'required|in:pending,in_progress,completed,cancelled',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator);
        }

        $oldStatus = $serviceRequest->status;
        $serviceRequest->update(['status' => $request->status]);

        // Send automatic message about status change
        $statusMessages = [
            'in_progress' => 'تم بدء العمل على طلبكم. سنقوم بإبلاغكم بالتحديثات قريباً.',
            'completed' => 'تم إكمال طلبكم بنجاح. شكراً لثقتكم بنا.',
            'cancelled' => 'تم إلغاء طلبكم. إذا كان لديكم أي استفسارات، يرجى التواصل معنا.',
        ];

        if (isset($statusMessages[$request->status])) {
            Message::create([
                'service_request_id' => $serviceRequest->id,
                'sender_id' => Auth::id(),
                'message' => $statusMessages[$request->status],
                'is_read' => false,
            ]);
        }

        return back()->with('success', 'تم تحديث حالة الطلب بنجاح!');
    }

    /**
     * Send a message to the service request.
     */
    public function sendMessage(Request $request, ServiceRequest $serviceRequest)
    {
        $validator = Validator::make($request->all(), [
            'message' => 'required|string',
            'files.*' => 'nullable|file|max:10240', // 10MB max per file
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator);
        }

        // Create message
        $message = Message::create([
            'service_request_id' => $serviceRequest->id,
            'sender_id' => Auth::id(),
            'message' => $request->message,
            'is_read' => false,
        ]);

        // Handle file uploads
        if ($request->hasFile('files')) {
            foreach ($request->file('files') as $file) {
                $fileName = $file->getClientOriginalName();
                $filePath = $file->store('admin_messages/' . $message->id, 'public');
                
                MessageFile::create([
                    'message_id' => $message->id,
                    'file_name' => $fileName,
                    'file_path' => $filePath,
                    'file_type' => $file->getClientMimeType(),
                ]);
            }
        }

        // If request is pending, mark as in progress
        if ($serviceRequest->isPending()) {
            $serviceRequest->markAsInProgress();
        }

        return back()->with('success', 'تم إرسال الرسالة بنجاح!');
    }

    /**
     * Show service types management.
     */
    public function serviceTypes()
    {
        $serviceTypes = ServiceType::withCount(['serviceRequests'])->with(['serviceRequests'])->get();
        return view('admin.services.types', compact('serviceTypes'));
    }

    /**
     * Store a new service type.
     */
    public function storeServiceType(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:service_types',
            'description' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        ServiceType::create([
            'name' => $request->name,
            'description' => $request->description,
        ]);

        return back()->with('success', 'تم إضافة نوع الخدمة بنجاح!');
    }

    /**
     * Update a service type.
     */
    public function updateServiceType(Request $request, ServiceType $serviceType)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:service_types,name,' . $serviceType->id,
            'description' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator);
        }

        $serviceType->update([
            'name' => $request->name,
            'description' => $request->description,
        ]);

        return back()->with('success', 'تم تحديث نوع الخدمة بنجاح!');
    }

    /**
     * Delete a service type.
     */
    public function destroyServiceType(ServiceType $serviceType)
    {
        // Check if service type has any requests
        if ($serviceType->serviceRequests()->exists()) {
            return back()->with('error', 'لا يمكن حذف نوع الخدمة لأنه يحتوي على طلبات.');
        }

        $serviceType->delete();

        return back()->with('success', 'تم حذف نوع الخدمة بنجاح!');
    }

    /**
     * Export service requests data.
     */
    public function export(Request $request)
    {
        $query = ServiceRequest::with(['user', 'serviceType']);

        // Apply same filters as index
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('service_type')) {
            $query->where('service_type_id', $request->service_type);
        }
        if ($request->filled('date_from')) {
            $query->where('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->where('created_at', '<=', $request->date_to . ' 23:59:59');
        }

        $serviceRequests = $query->get();

        $csvData = "العنوان,نوع الخدمة,اسم المستخدم,البريد الإلكتروني,الحالة,تاريخ الإنشاء\n";
        
        foreach ($serviceRequests as $request) {
            $csvData .= sprintf(
                "%s,%s,%s,%s,%s,%s\n",
                $request->title,
                $request->serviceType->name,
                $request->user->name,
                $request->user->email,
                $request->status_in_arabic,
                $request->created_at->format('Y-m-d H:i:s')
            );
        }

        $fileName = 'service_requests_export_' . now()->format('Y_m_d_H_i_s') . '.csv';
        
        return response($csvData)
            ->header('Content-Type', 'text/csv; charset=UTF-8')
            ->header('Content-Disposition', 'attachment; filename="' . $fileName . '"')
            ->header('Content-Encoding', 'UTF-8');
    }

    /**
     * Show statistics for service requests.
     */
    public function statistics()
    {
        $stats = [
            'total' => ServiceRequest::count(),
            'pending' => ServiceRequest::where('status', 'pending')->count(),
            'in_progress' => ServiceRequest::where('status', 'in_progress')->count(),
            'completed' => ServiceRequest::where('status', 'completed')->count(),
            'cancelled' => ServiceRequest::where('status', 'cancelled')->count(),
        ];

        // Monthly statistics
        $monthlyStats = ServiceRequest::selectRaw('
                YEAR(created_at) as year,
                MONTH(created_at) as month,
                COUNT(*) as total,
                SUM(CASE WHEN status = "completed" THEN 1 ELSE 0 END) as completed
            ')
            ->where('created_at', '>=', now()->subMonths(12))
            ->groupBy('year', 'month')
            ->orderBy('year', 'desc')
            ->orderBy('month', 'desc')
            ->get();

        // Service type statistics
        $serviceTypeStats = ServiceType::withCount(['serviceRequests'])->with(['serviceRequests'])->get();

        return view('admin.services.statistics', compact('stats', 'monthlyStats', 'serviceTypeStats'));
    }
public function edit(ServiceRequest $serviceRequest)
{
    $serviceTypes = ServiceType::all();
    return view('admin.services.edit', compact('serviceRequest', 'serviceTypes'));
}

public function update(Request $request, ServiceRequest $serviceRequest)
{
    $request->validate([
        'title' => 'required|string|max:255',
        'description' => 'required|string',
        'requirements' => 'nullable|string',
        'service_type_id' => 'required|exists:service_types,id',
        'status' => 'required|in:pending,in_progress,completed,cancelled',
    ]);

    $serviceRequest->update($request->only([
        'title',
        'description',
        'requirements',
        'service_type_id',
        'status',
    ]));

    return redirect()->route('admin.services.index')->with('success', 'تم تحديث الطلب بنجاح.');
}
/**
 * Delete a service request.
 */
public function destroy(ServiceRequest $serviceRequest)
{
    // يمكنك إضافة تحقق إضافي هنا إذا أردت
    $serviceRequest->delete();

    return redirect()->route('admin.services.index')->with('success', 'تم حذف طلب الخدمة بنجاح!');
}


}

