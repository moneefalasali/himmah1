#!/bin/bash

# سكريبت نشر مشروع همة على Laravel Cloud

echo "🚀 بدء عملية نشر مشروع همة..."

# تحديث التبعيات
echo "📦 تحديث التبعيات..."
composer install --optimize-autoloader --no-dev

# تنظيف وتحسين التطبيق
echo "🧹 تنظيف وتحسين التطبيق..."
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear

# تحسين للإنتاج
echo "⚡ تحسين للإنتاج..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# تشغيل المايجريشن
echo "🗄️ تشغيل قاعدة البيانات..."
php artisan migrate --force

# تعيين الصلاحيات
echo "🔐 تعيين الصلاحيات..."
chmod -R 755 storage/
chmod -R 755 bootstrap/cache/

# بناء الأصول (إذا كان يستخدم Vite أو Mix)
if [ -f "package.json" ]; then
    echo "🎨 بناء الأصول..."
    npm install
    npm run build
fi

echo "✅ تم الانتهاء من عملية النشر بنجاح!"
echo "🌐 يمكنك الآن الوصول للتطبيق"

