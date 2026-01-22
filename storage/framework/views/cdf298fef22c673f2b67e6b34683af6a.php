<?php $__env->startSection('content'); ?>
<div class="bg-gray-50 min-h-screen pb-20">
    <!-- Hero Section -->
    <div class="bg-white border-b border-gray-100 pt-24 pb-20 px-6">
        <div class="container mx-auto text-center">
            <span class="bg-blue-50 text-blue-600 px-4 py-1.5 rounded-full text-sm font-bold mb-6 inline-block uppercase tracking-widest">مركز المساعدة</span>
            <h1 class="text-4xl md:text-6xl font-black text-gray-900 mb-6 leading-tight">خدمات منصة همة</h1>
            <p class="text-xl text-gray-500 max-w-2xl mx-auto leading-relaxed">نحن هنا لمساعدتك في رحلتك التعليمية. اختر الخدمة التي تحتاجها وسيقوم فريقنا المتخصص بالرد عليك فوراً.</p>
        </div>
    </div>

    <div class="container mx-auto px-6 -mt-12">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-10 mb-20">
            <!-- بطاقة خدمة 1 -->
            <div class="bg-white p-10 rounded-[2.5rem] shadow-xl shadow-gray-200/50 border border-gray-50 hover:shadow-2xl hover:-translate-y-2 transition-all duration-500 text-center group">
                <div class="w-20 h-20 bg-blue-50 text-blue-600 rounded-3xl flex items-center justify-center mx-auto mb-8 text-3xl group-hover:bg-blue-600 group-hover:text-white transition-colors duration-500 transform group-hover:rotate-12">
                    <i class="fas fa-headset"></i>
                </div>
                <h3 class="text-2xl font-bold text-gray-800 mb-4">الدعم الفني</h3>
                <p class="text-gray-500 mb-8 leading-relaxed">حل المشكلات التقنية المتعلقة بالحساب، الدخول، أو تشغيل الفيديوهات التعليمية.</p>
                <a href="<?php echo e(env('PLATFORM_WHATSAPP') ? env('PLATFORM_WHATSAPP') : 'mailto:' . 'hemmah.platform.app@gmail.com'); ?>" target="_blank" rel="noopener" class="inline-block w-full bg-gray-50 text-blue-600 px-8 py-4 rounded-2xl font-bold hover:bg-blue-600 hover:text-white transition-all">تواصل الآن</a>
            </div>

            <!-- بطاقة خدمة 2 -->
            <div class="bg-white p-10 rounded-[2.5rem] shadow-xl shadow-gray-200/50 border border-gray-50 hover:shadow-2xl hover:-translate-y-2 transition-all duration-500 text-center group">
                <div class="w-20 h-20 bg-green-50 text-green-600 rounded-3xl flex items-center justify-center mx-auto mb-8 text-3xl group-hover:bg-green-600 group-hover:text-white transition-colors duration-500 transform group-hover:rotate-12">
                    <i class="fas fa-graduation-cap"></i>
                </div>
                <h3 class="text-2xl font-bold text-gray-800 mb-4">الاستشارات الأكاديمية</h3>
                <p class="text-gray-500 mb-8 leading-relaxed">مساعدتك في اختيار الدورات المناسبة لمسارك الدراسي وتخطيط مستقبلك التعليمي.</p>
                <a href="<?php echo e(env('PLATFORM_WHATSAPP') ? env('PLATFORM_WHATSAPP') : 'mailto:' . 'hemmah.platform.app@gmail.com'); ?>" target="_blank" rel="noopener" class="inline-block w-full bg-gray-50 text-green-600 px-8 py-4 rounded-2xl font-bold hover:bg-green-600 hover:text-white transition-all">اطلب استشارة</a>
            </div>

            <!-- بطاقة خدمة 3 -->
            <div class="bg-white p-10 rounded-[2.5rem] shadow-xl shadow-gray-200/50 border border-gray-50 hover:shadow-2xl hover:-translate-y-2 transition-all duration-500 text-center group">
                <div class="w-20 h-20 bg-purple-50 text-purple-600 rounded-3xl flex items-center justify-center mx-auto mb-8 text-3xl group-hover:bg-purple-600 group-hover:text-white transition-colors duration-500 transform group-hover:rotate-12">
                    <i class="fas fa-file-invoice-dollar"></i>
                </div>
                <h3 class="text-2xl font-bold text-gray-800 mb-4">خدمات الدفع</h3>
                <p class="text-gray-500 mb-8 leading-relaxed">استفسارات حول طرق الدفع المتاحة، الفواتير، أو طلبات استرداد الأموال.</p>
                <a href="<?php echo e(env('PLATFORM_WHATSAPP') ? env('PLATFORM_WHATSAPP') : 'mailto:' . 'hemmah.platform.app@gmail.com'); ?>" target="_blank" rel="noopener" class="inline-block w-full bg-gray-50 text-purple-600 px-8 py-4 rounded-2xl font-bold hover:bg-purple-600 hover:text-white transition-all">تواصل معنا</a>
            </div>
        </div>

        <!-- Special Request Section -->
        <div class="bg-gradient-to-br from-blue-600 to-blue-800 rounded-[3rem] p-12 md:p-20 text-white flex flex-col lg:flex-row items-center justify-between shadow-2xl shadow-blue-200 relative overflow-hidden">
            <div class="absolute top-0 right-0 w-64 h-64 bg-white/10 rounded-full -mr-32 -mt-32 blur-3xl"></div>
            <div class="absolute bottom-0 left-0 w-64 h-64 bg-blue-400/20 rounded-full -ml-32 -mb-32 blur-3xl"></div>
            
            <div class="relative z-10 mb-12 lg:mb-0 lg:mr-12 text-center lg:text-right">
                <h2 class="text-4xl md:text-5xl font-black mb-6">هل لديك طلب خاص؟</h2>
                <p class="text-blue-100 text-xl max-w-xl leading-relaxed">يمكنك فتح دردشة مباشرة مع فريق الإدارة لمناقشة أي طلبات أو خدمات مخصصة غير مدرجة أعلاه.</p>
            </div>
            <a href="<?php echo e(env('PLATFORM_WHATSAPP') ? env('PLATFORM_WHATSAPP') : 'mailto:' . 'hemmah.platform.app@gmail.com'); ?>" target="_blank" rel="noopener" class="relative z-10 bg-white text-blue-600 px-12 py-5 rounded-[2rem] font-black text-xl hover:bg-blue-50 transition-all shadow-xl hover:shadow-white/20 transform active:scale-95">
                <i class="fas fa-comments mr-3"></i> فتح دردشة مباشرة
            </a>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\himm23\resources\views/services/index.blade.php ENDPATH**/ ?>