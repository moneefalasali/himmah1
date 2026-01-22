<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
    @forelse($courses as $course)
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-md transition-shadow">
            <div class="relative">
                <img src="{{ $course->image_url }}" alt="{{ $course->title }}" class="w-full h-48 object-cover">
                <div class="absolute top-4 right-4">
                    <span class="px-3 py-1 rounded-full text-xs font-bold {{ $course->type === 'online' ? 'bg-green-100 text-green-700' : 'bg-blue-100 text-blue-700' }}">
                        {{ $course->type === 'online' ? 'أونلاين مباشر' : 'مسجل' }}
                    </span>
                </div>
            </div>
            
            <div class="p-6">
                <div class="flex items-center text-xs text-gray-500 mb-2">
                    <span>{{ $course->category->name }}</span>
                    <span class="mx-2">•</span>
                    <span>{{ $course->subject->name }}</span>
                </div>
                
                <h3 class="text-lg font-bold text-gray-900 mb-4 line-clamp-2">{{ $course->title }}</h3>
                
                <div class="flex items-center justify-between mt-auto">
                    <div class="flex items-center">
                        <div class="w-8 h-8 bg-gray-100 rounded-full flex items-center justify-center text-gray-600 text-xs font-bold">
                            {{ substr($course->instructor_name, 0, 1) }}
                        </div>
                        <span class="mr-2 text-sm text-gray-600">{{ $course->instructor_name }}</span>
                    </div>
                    
                    <div class="text-blue-600 font-bold">
                        {{ $course->price > 0 ? number_format($course->price) . ' ر.س' : 'مجاني' }}
                    </div>
                </div>
            </div>
            
            <div class="px-6 py-4 bg-gray-50 border-t border-gray-100">
                @auth
                    @if(auth()->user()->enrolledCourses()->where('courses.id', $course->id)->exists())
                        <span class="text-green-600 text-sm font-bold flex items-center">
                            <svg class="w-4 h-4 ml-1" fill="currentColor" viewBox="0 0 20 20"><path d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"></path></svg>
                            أنت مشترك في هذه الدورة
                        </span>
                    @else
                        <a href="#" class="text-blue-600 text-sm font-bold hover:underline">عرض التفاصيل والاشتراك</a>
                    @endif
                @else
                    <a href="{{ route('login') }}" class="text-blue-600 text-sm font-bold hover:underline">سجل دخول للاشتراك</a>
                @endauth
            </div>
        </div>
    @empty
        <div class="col-span-full py-20 text-center">
            <div class="text-gray-400 mb-4">
                <svg class="w-16 h-16 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M9.172 9.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
            <h3 class="text-xl font-bold text-gray-600">لا توجد دورات تطابق خيارات البحث</h3>
            <p class="text-gray-500 mt-2">جرب تغيير الفلاتر أو البحث عن كلمات أخرى</p>
        </div>
    @endforelse
</div>

<div class="mt-12">
    {{ $courses->links() }}
</div>
