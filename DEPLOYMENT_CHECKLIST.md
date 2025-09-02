# قائمة مراجعة نشر مشروع همة على Laravel Cloud

## قبل النشر

### ✅ إعداد الملفات
- [x] إنشاء ملف `.env.production`
- [x] إنشاء ملف `laravel-cloud.yml`
- [x] إنشاء ملف `Dockerfile`
- [x] إنشاء ملف `docker-compose.yml`
- [x] إنشاء ملف `.dockerignore`
- [x] إنشاء سكريپت `deploy.sh`
- [x] تحديث ملف `.gitignore`
- [x] إنشاء دليل النشر

### 📋 التحقق من الإعدادات
- [ ] التأكد من صحة إعدادات قاعدة البيانات
- [ ] التحقق من إعدادات البريد الإلكتروني
- [ ] مراجعة إعدادات PayTabs
- [ ] التأكد من وجود مفتاح التطبيق
- [ ] مراجعة إعدادات الأمان

### 🔧 الاختبار المحلي
- [ ] تشغيل المشروع محلياً
- [ ] اختبار الاتصال بقاعدة البيانات
- [ ] اختبار إرسال البريد الإلكتروني
- [ ] اختبار المدفوعات (بيئة الاختبار)
- [ ] التأكد من عمل جميع الصفحات

## أثناء النشر

### 🌐 إعداد Laravel Cloud
- [ ] إنشاء حساب Laravel Cloud
- [ ] ربط مستودع GitHub
- [ ] تكوين متغيرات البيئة
- [ ] اختيار خطة الاستضافة المناسبة

### 🚀 عملية النشر
- [ ] رفع الكود إلى GitHub
- [ ] تشغيل النشر من Laravel Cloud
- [ ] مراقبة سجلات البناء
- [ ] التحقق من عدم وجود أخطاء

### 🗄️ إعداد قاعدة البيانات
- [ ] تشغيل المايجريشن
- [ ] تشغيل البذور (إذا لزم الأمر)
- [ ] التحقق من البيانات الأساسية
- [ ] إنشاء حسابات المدراء

## بعد النشر

### 🧪 الاختبار الشامل
- [ ] اختبار الصفحة الرئيسية
- [ ] اختبار تسجيل الدخول
- [ ] اختبار إنشاء حساب جديد
- [ ] اختبار إدارة الطلاب
- [ ] اختبار إدارة المقررات
- [ ] اختبار نظام الدرجات
- [ ] اختبار نظام المدفوعات
- [ ] اختبار إرسال البريد الإلكتروني

### 🔒 الأمان والأداء
- [ ] التأكد من تشغيل HTTPS
- [ ] اختبار أداء الموقع
- [ ] مراجعة سجلات الأخطاء
- [ ] تفعيل المراقبة
- [ ] إعداد النسخ الاحتياطية

### 📊 المراقبة والصيانة
- [ ] إعداد تنبيهات الأخطاء
- [ ] مراقبة استخدام الموارد
- [ ] جدولة النسخ الاحتياطية
- [ ] إعداد المهام المجدولة
- [ ] توثيق معلومات الوصول

## قائمة المتغيرات المطلوبة

### متغيرات التطبيق
```
APP_NAME=منصة همة
APP_ENV=production
APP_DEBUG=false
APP_URL=https://your-domain.com
APP_KEY=[سيتم إنشاؤه تلقائياً]
```

### متغيرات قاعدة البيانات
```
DB_CONNECTION=mysql
DB_HOST=[من Laravel Cloud]
DB_PORT=3306
DB_DATABASE=[من Laravel Cloud]
DB_USERNAME=[من Laravel Cloud]
DB_PASSWORD=[من Laravel Cloud]
```

### متغيرات البريد الإلكتروني
```
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=hemmah.platform.app@gmail.com
MAIL_PASSWORD=[كلمة مرور التطبيق]
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=hemmah.platform.app@gmail.com
MAIL_FROM_NAME=منصة همة
```

### متغيرات PayTabs
```
PAYTABS_PROFILE_ID=[من حساب PayTabs]
PAYTABS_SERVER_KEY=[من حساب PayTabs]
PAYTABS_BASE_URL=https://secure.paytabs.sa
```

### متغيرات إضافية
```
LOG_LEVEL=error
CACHE_DRIVER=redis
SESSION_DRIVER=database
QUEUE_CONNECTION=database
```

## معلومات مهمة

### 📞 معلومات الاتصال
- **البريد الإلكتروني**: hemmah.platform.app@gmail.com
- **كلمة مرور البريد**: sgomwxaohzhdvaxo (كلمة مرور التطبيق)

### 🔗 روابط مفيدة
- [Laravel Cloud Dashboard](https://cloud.laravel.com)
- [PayTabs Dashboard](https://secure.paytabs.sa)
- [GitHub Repository](https://github.com/username/himmah-platform)

### 📝 ملاحظات
- تأكد من تحديث جميع كلمات المرور والمفاتيح السرية
- احتفظ بنسخة احتياطية من ملف `.env.production`
- راجع السجلات بانتظام للتأكد من عدم وجود مشاكل
- قم بتحديث التبعيات بانتظام لضمان الأمان

---

**تاريخ آخر تحديث**: [التاريخ الحالي]
**حالة المشروع**: جاهز للنشر ✅

