<?php $__env->startSection('content'); ?>
<div class="container mx-auto p-8">
    <div class="max-w-4xl mx-auto">
        <h1 class="text-3xl font-bold mb-8 text-gray-800">إعدادات الملف الشخصي</h1>
        
        <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
            <form action="<?php echo e(route('profile.update')); ?>" method="POST" enctype="multipart/form-data">
                <?php echo csrf_field(); ?>
                <?php echo method_field('PUT'); ?>
                
                <div class="p-8 border-b border-gray-50 bg-gray-50/50">
                    <div class="flex items-center">
                        <div class="relative group">
                            <img src="<?php echo e(auth()->user()->avatar_url); ?>" class="w-24 h-24 rounded-2xl object-cover border-4 border-white shadow-md">
                            <label class="absolute inset-0 flex items-center justify-center bg-black/40 text-white rounded-2xl opacity-0 group-hover:opacity-100 cursor-pointer transition">
                                <i class="fas fa-camera"></i>
                                <input type="file" name="avatar" class="hidden">
                            </label>
                        </div>
                        <div class="mr-6">
                            <h2 class="text-xl font-bold text-gray-800"><?php echo e(auth()->user()->name); ?></h2>
                            <p class="text-gray-500"><?php echo e(auth()->user()->email); ?></p>
                        </div>
                    </div>
                </div>

                <div class="p-8 space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">الاسم الكامل</label>
                            <input type="text" name="name" value="<?php echo e(auth()->user()->name); ?>" class="w-full border-gray-200 rounded-xl focus:ring-blue-500 focus:border-blue-500 py-3">
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">البريد الإلكتروني</label>
                            <input type="email" name="email" value="<?php echo e(auth()->user()->email); ?>" class="w-full border-gray-200 rounded-xl focus:ring-blue-500 focus:border-blue-500 py-3">
                        </div>
                    </div>

                    <div class="pt-6 border-t border-gray-50">
                        <h3 class="text-lg font-bold mb-4 text-gray-800">تغيير كلمة المرور</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-2">كلمة المرور الجديدة</label>
                                <input type="password" name="password" class="w-full border-gray-200 rounded-xl focus:ring-blue-500 focus:border-blue-500 py-3">
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-2">تأكيد كلمة المرور</label>
                                <input type="password" name="password_confirmation" class="w-full border-gray-200 rounded-xl focus:ring-blue-500 focus:border-blue-500 py-3">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="p-8 bg-gray-50 border-t border-gray-100 flex justify-end">
                    <button type="submit" class="bg-blue-600 text-white px-10 py-3 rounded-xl font-bold hover:bg-blue-700 transition shadow-lg">
                        حفظ التغييرات
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\himm23\resources\views/user/profile.blade.php ENDPATH**/ ?>