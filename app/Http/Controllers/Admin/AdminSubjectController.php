<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AdminSubjectController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'admin']);
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $subjects = Subject::with('category')->latest()->paginate(10);
        return view('admin.subjects.index', compact('subjects'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = Category::all();
        return view('admin.subjects.create', compact('categories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:subjects,name',
            'category_id' => 'required|exists:categories,id',
        ]);

        Subject::create([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'category_id' => $request->category_id,
        ]);

        return redirect()->route('admin.subjects.index')->with('success', 'تم إنشاء المقرر بنجاح.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Subject $subject)
    {
        $categories = Category::all();
        return view('admin.subjects.edit', compact('subject', 'categories'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Subject $subject)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:subjects,name,' . $subject->id,
            'category_id' => 'required|exists:categories,id',
        ]);

        $subject->update([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'category_id' => $request->category_id,
        ]);

        return redirect()->route('admin.subjects.index')->with('success', 'تم تحديث المقرر بنجاح.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Subject $subject)
    {
        if ($subject->courses()->count() > 0) {
            return back()->with('error', 'لا يمكن حذف المقرر لوجود دورات مرتبطة به.');
        }
        $subject->delete();
        return redirect()->route('admin.subjects.index')->with('success', 'تم حذف المقرر بنجاح.');
    }
}
