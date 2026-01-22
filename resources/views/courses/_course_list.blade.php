@foreach($courses as $course)
    <div class="bg-white rounded-[2rem] shadow-sm border border-gray-100 overflow-hidden hover:shadow-2xl transition-all duration-500 group flex flex-col h-full">
        <div class="relative overflow-hidden">
            <img src="{{ $course->thumbnail_url }}" class="w-full h-64 object-cover group-hover:scale-110 transition duration-700">
            <div class="absolute top-5 right-5 bg-white/95 backdrop-blur px-4 py-1.5 rounded-full text-xs font-black text-blue-700 shadow-lg uppercase tracking-wider">
                {{ $course->type === 'recorded' ? 'مسجل' : 'أونلاين' }}
            </div>
            <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-500 flex items-end p-8">
                <span class="text-white font-bold text-sm"><i class="fas fa-play-circle mr-2"></i> عرض التفاصيل</span>
            </div>
        </div>
        <div class="p-8 flex flex-col flex-grow">
            <div class="flex items-center justify-between mb-4">
                <span class="bg-blue-50 text-blue-600 px-3 py-1 rounded-lg text-xs font-bold">{{ $course->category->name ?? 'عام' }}</span>
                <span class="text-gray-400 text-xs font-medium"><i class="far fa-clock mr-1"></i> {{ $course->total_duration ?? '0' }} ساعة</span>
            </div>
            <h3 class="text-2xl font-bold text-gray-800 mb-4 group-hover:text-blue-600 transition-colors line-clamp-1">{{ $course->title }}</h3>
            <p class="text-gray-500 text-sm mb-8 line-clamp-2 leading-relaxed">{{ $course->description }}</p>
            
            <div class="mt-auto pt-6 border-t border-gray-50 flex items-center justify-between">
                <div class="flex flex-col">
                    <span class="text-gray-400 text-xs font-bold uppercase tracking-widest mb-1">السعر</span>
                    <div class="text-3xl font-black text-blue-600">{{ $course->price }} <span class="text-sm font-bold">ريال</span></div>
                </div>
                <a href="{{ route('courses.show', $course) }}" class="bg-blue-600 text-white px-8 py-3.5 rounded-2xl font-bold hover:bg-blue-700 hover:shadow-lg hover:shadow-blue-200 transition-all transform active:scale-95">
                    اشترك الآن
                </a>
            </div>
        </div>
    </div>
@endforeach
