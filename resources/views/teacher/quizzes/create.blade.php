@extends('layouts.app')

@section('title', 'إنشاء اختبار جديد')

@section('content')
<div class="container mx-auto p-6">
    <div class="max-w-4xl mx-auto">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold">إنشاء اختبار جديد</h1>
            <a href="{{ route('teacher.quizzes.index') }}" class="text-blue-600 hover:underline">العودة للقائمة</a>
        </div>

        <div class="bg-white rounded-xl shadow-sm p-8">
            <form action="{{ route('teacher.quizzes.store') }}" method="POST">
                @csrf
                
                <div class="space-y-6">
                    <!-- اختيار الكورس -->
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">اختر الكورس المرتبط</label>
                        <select name="course_id" class="w-full border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500" required>
                            <option value="">اختر الكورس...</option>
                            @foreach($courses as $course)
                                <option value="{{ $course->id }}">{{ $course->title }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- عنوان الاختبار -->
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">عنوان الاختبار</label>
                        <input type="text" name="title" class="w-full border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500" required>
                    </div>

                    <!-- الوصف -->
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">وصف الاختبار</label>
                        <textarea name="description" rows="3" class="w-full border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"></textarea>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- الوقت -->
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">الوقت المحدد (بالدقائق)</label>
                            <input type="number" name="duration_minutes" class="w-full border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500" min="1">
                        </div>

                        <!-- الحالة -->
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">الحالة</label>
                            <select name="status" class="w-full border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                                <option value="draft">مسودة</option>
                                <option value="published">نشر</option>
                            </select>
                        </div>
                    </div>

                    <div class="pt-6 border-t">
                        <button type="submit" class="w-full bg-blue-600 text-white font-bold py-3 rounded-lg hover:bg-blue-700 transition">
                            إنشاء الاختبار والبدء بإضافة الأسئلة
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
