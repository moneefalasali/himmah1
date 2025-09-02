# منصة همة - نظام إدارة جامعي

## نظرة عامة

منصة همة هي نظام إدارة جامعي شامل مبني بإطار عمل Laravel 10. يوفر النظام إدارة متكاملة للطلاب والمقررات والدرجات والمدفوعات.

## المميزات الرئيسية

- **إدارة الطلاب**: تسجيل وإدارة بيانات الطلاب
- **إدارة المقررات**: إنشاء وتنظيم المقررات الدراسية
- **نظام الدرجات**: تسجيل وتتبع درجات الطلاب
- **نظام المدفوعات**: تكامل مع PayTabs للمدفوعات الإلكترونية
- **نظام البريد الإلكتروني**: إرسال الإشعارات والتنبيهات
- **لوحة تحكم شاملة**: واجهة إدارية سهلة الاستخدام

## التقنيات المستخدمة

- **Backend**: Laravel 10, PHP 8.1+
- **Database**: MySQL 8.0
- **Cache**: Redis
- **Frontend**: Blade Templates, Vite
- **Payment**: PayTabs Integration
- **Email**: PHPMailer with Gmail SMTP

## متطلبات النظام

- PHP 8.1 أو أحدث
- MySQL 8.0
- Redis
- Composer
- Node.js 18+
- NPM

## التثبيت المحلي

### 1. نسخ المشروع
```bash
git clone https://github.com/username/himmah-platform.git
cd himmah-platform
```

### 2. تثبيت التبعيات
```bash
composer install
npm install
```

### 3. إعداد البيئة
```bash
cp .env.example .env
php artisan key:generate
```

### 4. إعداد قاعدة البيانات
```bash
php artisan migrate
php artisan db:seed
```

### 5. بناء الأصول
```bash
npm run dev
# أو للإنتاج
npm run build
```

### 6. تشغيل الخادم
```bash
php artisan serve
```

## النشر على Laravel Cloud

يتضمن المشروع جميع الملفات اللازمة للنشر على Laravel Cloud:

- `laravel-cloud.yml` - ملف التكوين الرئيسي
- `.env.production` - إعدادات الإنتاج
- `deploy.sh` - سكريپت النشر الآلي
- `Dockerfile` - لبناء الحاوية
- `docker-compose.yml` - للتطوير المحلي

### خطوات النشر السريعة

1. ارفع الكود إلى GitHub
2. اربط المستودع بـ Laravel Cloud
3. أضف متغيرات البيئة المطلوبة
4. انقر على "Deploy"

للتفاصيل الكاملة، راجع [دليل النشر](LARAVEL_CLOUD_DEPLOYMENT_GUIDE.md).

## إعداد المتغيرات

### متغيرات أساسية مطلوبة
```env
APP_NAME="منصة همة"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://your-domain.com
```

### إعدادات قاعدة البيانات
```env
DB_CONNECTION=mysql
DB_HOST=your-db-host
DB_DATABASE=your-db-name
DB_USERNAME=your-db-user
DB_PASSWORD=your-db-password
```

### إعدادات البريد الإلكتروني
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=hemmah.platform.app@gmail.com
MAIL_PASSWORD=your-app-password
```

### إعدادات PayTabs
```env
PAYTABS_PROFILE_ID=your-profile-id
PAYTABS_SERVER_KEY=your-server-key
PAYTABS_BASE_URL=https://secure.paytabs.sa
```

## الاستخدام

### حسابات الاختبار

يمكنك العثور على حسابات الاختبار في ملف [TEST_ACCOUNTS.md](TEST_ACCOUNTS.md).

### الوثائق

- [دليل التثبيت](INSTALLATION_GUIDE.md)
- [وثائق النظام](UNIVERSITY_SYSTEM_DOCUMENTATION.md)
- [دليل النشر](LARAVEL_CLOUD_DEPLOYMENT_GUIDE.md)

## الهيكل

```
himmah-platform/
├── app/                    # منطق التطبيق
├── config/                 # ملفات التكوين
├── database/              # المايجريشن والبذور
├── public/                # الملفات العامة
├── resources/             # القوالب والأصول
├── routes/                # ملفات التوجيه
├── storage/               # ملفات التخزين
├── docker/                # ملفات Docker
├── deploy.sh              # سكريپت النشر
├── laravel-cloud.yml      # تكوين Laravel Cloud
└── .env.production        # إعدادات الإنتاج
```

## المساهمة

1. Fork المشروع
2. إنشاء فرع للميزة الجديدة (`git checkout -b feature/AmazingFeature`)
3. Commit التغييرات (`git commit -m 'Add some AmazingFeature'`)
4. Push للفرع (`git push origin feature/AmazingFeature`)
5. إنشاء Pull Request

## الترخيص

هذا المشروع مرخص تحت رخصة MIT. راجع ملف [LICENSE](LICENSE) للتفاصيل.

## الدعم

للحصول على الدعم، يرجى التواصل عبر:
- البريد الإلكتروني: hemmah.platform.app@gmail.com
- إنشاء Issue في GitHub

## سجل التغييرات

راجع ملف [CHANGELOG.md](CHANGELOG.md) لمعرفة آخر التحديثات والتغييرات.

---

**تم تجهيز المشروع للنشر على Laravel Cloud** ✅

