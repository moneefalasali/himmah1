<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\University;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AdminUniversityController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'admin']);
    }

    /**
     * Display a listing of universities.
     */
    public function index()
    {
        $universities = University::withCount('users')->orderBy('name')->paginate(15);
        return view('admin.universities.index', compact('universities'));
    }

    /**
     * Show the form for creating a new university.
     */
    public function create()
    {
        return view('admin.universities.create');
    }

    /**
     * Store a newly created university in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:universities',
            'city' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        University::create([
            'name' => $request->name,
            'city' => $request->city,
        ]);

        return redirect()->route('admin.universities.index')
            ->with('success', 'تم إنشاء الجامعة بنجاح');
    }

    /**
     * Display the specified university.
     */
    public function show(University $university)
    {
        $university->load(['users', 'uniCourses.course']);
        return view('admin.universities.show', compact('university'));
    }

    /**
     * Show the form for editing the specified university.
     */
    public function edit(University $university)
    {
        return view('admin.universities.edit', compact('university'));
    }

    /**
     * Update the specified university in storage.
     */
    public function update(Request $request, University $university)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:universities,name,' . $university->id,
            'city' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $university->update([
            'name' => $request->name,
            'city' => $request->city,
        ]);

        return redirect()->route('admin.universities.index')
            ->with('success', 'تم تحديث الجامعة بنجاح');
    }

    /**
     * Remove the specified university from storage.
     */
    public function destroy(University $university)
    {
        // Check if university has users
        if ($university->users()->count() > 0) {
            return back()->with('error', 'لا يمكن حذف الجامعة لأنها مرتبطة بمستخدمين');
        }

        $university->delete();

        return redirect()->route('admin.universities.index')
            ->with('success', 'تم حذف الجامعة بنجاح');
    }
}

