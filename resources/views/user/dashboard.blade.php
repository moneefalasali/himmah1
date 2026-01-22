@extends('layouts.app')

@section('content')
<div class="p-6 bg-gray-50 min-h-screen">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-800">مرحباً بك، {{ auth()->user()->name }}</h1>
        <p class="text-gray-600">تابع تقدمك في دوراتك التعليمية.</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- My Enrolled Courses -->
        <div class="lg:col-span-2">
            <h3 class="text-xl font-bold mb-6">دوراتي الحالية</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                @forelse($enrolledCourses ?? [] as $course)
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-md transition">
                        <img src="{{ $course->thumbnail_url }}" class="w-full h-40 object-cover">
                        <div class="p-5">
                            <h4 class="font-bold text-gray-800 mb-2">{{ $course->title }}</h4>
                            <div class="w-full bg-gray-100 rounded-full h-2 mb-4">
                                <div class="bg-blue-600 h-2 rounded-full" style="width: {{ $course->progress ?? 0 }}%"></div>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-xs text-gray-500">{{ $course->progress ?? 0 }}% مكتمل</span>
                                <a href="{{ route('lessons.show', $course->lessons->first()) }}" class="text-blue-600 font-bold text-sm hover:underline">متابعة التعلم</a>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-span-2 bg-white p-12 rounded-2xl border border-dashed border-gray-300 text-center">
                        <p class="text-gray-500 mb-4">أنت غير مسجل في أي دورة حالياً.</p>
                        <a href="{{ route('courses.index') }}" class="inline-block bg-blue-600 text-white px-8 py-3 rounded-xl font-bold hover:bg-blue-700 transition">استكشف الدورات</a>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Sidebar: Quick Links & Support -->
        <div class="space-y-8">
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
                <h3 class="font-bold mb-4 border-b pb-2">روابط سريعة</h3>
                <div class="space-y-3">
                    <a href="{{ route('chat.admin') }}" class="flex items-center p-3 bg-blue-50 text-blue-700 rounded-xl hover:bg-blue-100 transition">
                        <i class="fas fa-headset mr-3"></i>
                        <span class="text-sm font-bold">تواصل مع الدعم الفني</span>
                    </a>
                    <a href="{{ route('user.profile') }}" class="flex items-center p-3 bg-gray-50 text-gray-700 rounded-xl hover:bg-gray-100 transition">
                        <i class="fas fa-user-cog mr-3"></i>
                        <span class="text-sm font-bold">إعدادات الحساب</span>
                    </a>
                    <a href="{{ route('courses.index') }}" class="flex items-center p-3 bg-green-50 text-green-700 rounded-xl hover:bg-green-100 transition">
                        <i class="fas fa-search mr-3"></i>
                        <span class="text-sm font-bold">البحث عن دورات جديدة</span>
                    </a>
                </div>
            </div>

            <!-- AI Assistant Widget -->
            <div class="bg-gradient-to-br from-blue-600 to-purple-700 p-6 rounded-2xl shadow-xl text-white">
                <h3 class="font-bold mb-2 flex items-center">
                    <i class="fas fa-robot mr-2"></i> مساعد همة الذكي
                </h3>
                <p class="text-blue-100 text-xs mb-4">لديك سؤال حول دروسك؟ اسأل المساعد الذكي الآن!</p>
                <button @click="openAIChat()" class="w-full bg-white text-blue-600 font-bold py-2 rounded-lg hover:bg-blue-50 transition text-sm">
                    ابدأ المحادثة
                </button>
            </div>
        </div>
    </div>
</div>
@endsection
