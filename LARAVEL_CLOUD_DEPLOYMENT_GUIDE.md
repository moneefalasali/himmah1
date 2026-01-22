# دليل نشر مشروع همة على Laravel Cloud

## نظرة عامة

هذا الدليل يوضح كيفية نشر مشروع همة (Himmah) على منصة Laravel Cloud. المشروع عبارة عن نظام إدارة جامعي مبني بإطار عمل Laravel 10.

## متطلبات النشر

### المتطلبات التقنية
- PHP 8.1 أو أحدث
- MySQL 8.0
- Redis للتخزين المؤقت
- Node.js 18+ لبناء الأصول
- Composer لإدارة التبعيات

### الحسابات المطلوبة
- حساب Laravel Cloud
- حساب GitHub أو GitLab لاستضافة الكود
- حساب Gmail للبريد الإلكتروني (مُعد مسبقاً)
- حساب PayTabs للمدفوعات

## الملفات المُضافة للنشر

تم إضافة الملفات التالية لتجهيز المشروع للنشر:

### 1. ملفات التكوين
- `.env.production` - إعدادات البيئة للإنتاج
- `laravel-cloud.yml` - ملف تكوين Laravel Cloud
- `Dockerfile` - لبناء صورة Docker
- `docker-compose.yml` - للتطوير المحلي
- `.dockerignore` - لتحسين بناء الصورة

### 2. ملفات النشر
- `deploy.sh` - سكريبت النشر الآلي
- `docker/apache.conf` - إعدادات خادم Apache

## خطوات النشر

### الخطوة 1: إعداد المستودع

1. قم بإنشاء مستودع جديد على GitHub:
   ```bash
   git init
   git add .
   git commit -m "Initial commit - Himmah University System"
   git branch -M main
   git remote add origin https://github.com/username/himmah-platform.git
   git push -u origin main
   ```

### الخطوة 2: إعداد Laravel Cloud

1. سجل الدخول إلى [Laravel Cloud](https://cloud.laravel.com)
2. انقر على "Create New Project"
3. اختر مستودع GitHub الخاص بك
4. حدد الفرع `main`

### الخطوة 3: تكوين متغيرات البيئة

في لوحة تحكم Laravel Cloud، أضف المتغيرات التالية:

#### إعدادات التطبيق الأساسية
```
APP_NAME=منصة همة
APP_ENV=production
APP_DEBUG=false
APP_URL=https://your-domain.com
```

#### إعدادات قاعدة البيانات
```
DB_CONNECTION=mysql
DB_HOST=[سيتم توفيرها بواسطة Laravel Cloud]
DB_PORT=3306
DB_DATABASE=[سيتم توفيرها بواسطة Laravel Cloud]
DB_USERNAME=[سيتم توفيرها بواسطة Laravel Cloud]
DB_PASSWORD=[سيتم توفيرها بواسطة Laravel Cloud]
```

#### إعدادات البريد الإلكتروني
```
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=hemmah.platform.app@gmail.com
MAIL_PASSWORD=sgomwxaohzhdvaxo
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=hemmah.platform.app@gmail.com
MAIL_FROM_NAME=منصة همة
```

#### إعدادات PayTabs
```
PAYTABS_PROFILE_ID=[يجب الحصول عليه من PayTabs]
PAYTABS_SERVER_KEY=[يجب الحصول عليه من PayTabs]
PAYTABS_BASE_URL=https://secure.paytabs.sa
```

### الخطوة 4: تكوين الخدمات

#### قاعدة البيانات
- Laravel Cloud سيوفر قاعدة بيانات MySQL تلقائياً
- تأكد من تشغيل المايجريشن بعد النشر

#### التخزين المؤقت
- سيتم تكوين Redis تلقائياً
- تأكد من تعيين `CACHE_DRIVER=redis`

#### التخزين
- يمكن استخدام S3 للملفات الكبيرة
- أو الاعتماد على التخزين المحلي للبداية

### الخطوة 5: النشر

1. في لوحة تحكم Laravel Cloud، انقر على "Deploy"
2. انتظر حتى اكتمال عملية البناء
3. تحقق من السجلات للتأكد من عدم وجود أخطاء

## ما بعد النشر

### 1. تشغيل المايجريشن
```bash
php artisan migrate --force
```

### 2. إنشاء مفتاح التطبيق (إذا لم يكن موجوداً)
```bash
php artisan key:generate
```

### 3. تحسين الأداء
```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### 4. إعداد المهام المجدولة
تأكد من تكوين Cron Job لتشغيل:
```bash
* * * * * php artisan schedule:run
```

## اختبار التطبيق

بعد النشر، تحقق من:

1. **الصفحة الرئيسية**: تأكد من تحميل الموقع بشكل صحيح
2. **تسجيل الدخول**: اختبر نظام المصادقة
3. **قاعدة البيانات**: تأكد من عمل الاتصال بقاعدة البيانات
4. **البريد الإلكتروني**: اختبر إرسال الرسائل
5. **المدفوعات**: اختبر تكامل PayTabs (في بيئة الاختبار)

## استكشاف الأخطاء

### مشاكل شائعة وحلولها

#### خطأ 500 - Internal Server Error
- تحقق من ملف `.env` والمتغيرات
- تأكد من وجود مفتاح التطبيق
- راجع سجلات الأخطاء

#### مشاكل قاعدة البيانات
- تحقق من إعدادات الاتصال
- تأكد من تشغيل المايجريشن
- راجع صلاحيات المستخدم

#### مشاكل الأصول (CSS/JS)
- تأكد من تشغيل `npm run build`
- تحقق من إعدادات Vite
- راجع مسارات الملفات

## الأمان والصيانة

### إعدادات الأمان
1. تأكد من تعيين `APP_DEBUG=false` في الإنتاج
2. استخدم HTTPS دائماً
3. قم بتحديث التبعيات بانتظام
4. راقب السجلات للأنشطة المشبوهة

### النسخ الاحتياطية
- Laravel Cloud يوفر نسخ احتياطية تلقائية لقاعدة البيانات
- قم بإعداد نسخ احتياطية إضافية للملفات المهمة

### المراقبة
- استخدم أدوات مراقبة الأداء
- راقب استخدام الموارد
- تتبع الأخطاء والاستثناءات

## الدعم والمساعدة

### الموارد المفيدة
- [وثائق Laravel](https://laravel.com/docs)
- [وثائق Laravel Cloud](https://cloud.laravel.com/docs)
- [مجتمع Laravel](https://laracasts.com)

### معلومات الاتصال
- البريد الإلكتروني: hemmah.platform.app@gmail.com
- الدعم التقني: [حسب الحاجة]

---

**ملاحظة**: هذا الدليل يغطي الخطوات الأساسية للنشر. قد تحتاج إلى تخصيصات إضافية حسب متطلبات مشروعك المحددة.

