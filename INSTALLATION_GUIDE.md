# دليل التثبيت - نظام الجامعات

## متطلبات النظام

### متطلبات الخادم
- PHP 8.1 أو أحدث
- MySQL 8.0 أو أحدث
- Composer
- Node.js و npm
- خادم ويب (Apache/Nginx)

### امتدادات PHP المطلوبة
- BCMath PHP Extension
- Ctype PHP Extension
- Fileinfo PHP Extension
- JSON PHP Extension
- Mbstring PHP Extension
- OpenSSL PHP Extension
- PDO PHP Extension
- Tokenizer PHP Extension
- XML PHP Extension

## خطوات التثبيت

### 1. تحضير البيئة

#### تحديث النظام
```bash
sudo apt update && sudo apt upgrade -y
```

#### تثبيت PHP والامتدادات
```bash
sudo apt install php8.1 php8.1-cli php8.1-fpm php8.1-mysql php8.1-xml php8.1-curl php8.1-gd php8.1-mbstring php8.1-zip php8.1-bcmath -y
```

#### تثبيت Composer
```bash
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer
```

#### تثبيت MySQL
```bash
sudo apt install mysql-server -y
sudo mysql_secure_installation
```

### 2. إعداد المشروع

#### استنساخ المشروع
```bash
git clone [repository-url] himmah-platform
cd himmah-platform
```

#### تثبيت التبعيات
```bash
composer install
npm install && npm run build
```

#### إعداد ملف البيئة
```bash
cp .env.example .env
php artisan key:generate
```

#### تحرير ملف .env
```env
APP_NAME="منصة همة التعليمية"
APP_ENV=production
APP_KEY=base64:...
APP_DEBUG=false
APP_URL=https://yourdomain.com

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=himmah_platform
DB_USERNAME=your_username
DB_PASSWORD=your_password

# إعدادات البريد الإلكتروني
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your_email@gmail.com
MAIL_PASSWORD=your_app_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@yourdomain.com
MAIL_FROM_NAME="${APP_NAME}"

# إعدادات الدفع (PayTabs)
PAYTABS_PROFILE_ID=your_profile_id
PAYTABS_SERVER_KEY=your_server_key
PAYTABS_CLIENT_KEY=your_client_key
PAYTABS_CURRENCY=SAR
PAYTABS_REGION=SAU

# إعدادات Vimeo
VIMEO_CLIENT_ID=your_vimeo_client_id
VIMEO_CLIENT_SECRET=your_vimeo_client_secret
VIMEO_ACCESS_TOKEN=your_vimeo_access_token
```

### 3. إعداد قاعدة البيانات

#### إنشاء قاعدة البيانات
```sql
CREATE DATABASE himmah_platform CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'himmah_user'@'localhost' IDENTIFIED BY 'secure_password';
GRANT ALL PRIVILEGES ON himmah_platform.* TO 'himmah_user'@'localhost';
FLUSH PRIVILEGES;
```

#### تشغيل الترحيلات
```bash
php artisan migrate
```

#### تشغيل البذور
```bash
php artisan db:seed
```

### 4. إعداد الصلاحيات

#### صلاحيات الملفات
```bash
sudo chown -R www-data:www-data storage bootstrap/cache
sudo chmod -R 775 storage bootstrap/cache
```

#### إنشاء الروابط الرمزية
```bash
php artisan storage:link
```

### 5. إعداد خادم الويب

#### Apache
إنشاء ملف Virtual Host:
```apache
<VirtualHost *:80>
    ServerName yourdomain.com
    DocumentRoot /var/www/himmah-platform/public
    
    <Directory /var/www/himmah-platform/public>
        AllowOverride All
        Require all granted
    </Directory>
    
    ErrorLog ${APACHE_LOG_DIR}/himmah_error.log
    CustomLog ${APACHE_LOG_DIR}/himmah_access.log combined
</VirtualHost>
```

#### Nginx
إنشاء ملف إعداد:
```nginx
server {
    listen 80;
    server_name yourdomain.com;
    root /var/www/himmah-platform/public;
    
    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";
    
    index index.php;
    
    charset utf-8;
    
    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }
    
    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }
    
    error_page 404 /index.php;
    
    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.1-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }
    
    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

### 6. إعداد SSL (اختياري ولكن مُوصى به)

#### استخدام Let's Encrypt
```bash
sudo apt install certbot python3-certbot-apache -y
sudo certbot --apache -d yourdomain.com
```

### 7. إعداد المهام المجدولة (Cron Jobs)

#### إضافة مهمة Laravel Scheduler
```bash
crontab -e
```

إضافة السطر التالي:
```cron
* * * * * cd /var/www/himmah-platform && php artisan schedule:run >> /dev/null 2>&1
```

### 8. إعداد Queue Workers (اختياري)

#### إنشاء خدمة systemd
```bash
sudo nano /etc/systemd/system/himmah-worker.service
```

محتوى الملف:
```ini
[Unit]
Description=Himmah Queue Worker
After=network.target

[Service]
Type=simple
User=www-data
WorkingDirectory=/var/www/himmah-platform
ExecStart=/usr/bin/php artisan queue:work --sleep=3 --tries=3 --max-time=3600
Restart=always
RestartSec=5

[Install]
WantedBy=multi-user.target
```

تفعيل الخدمة:
```bash
sudo systemctl enable himmah-worker
sudo systemctl start himmah-worker
```

## التحقق من التثبيت

### 1. اختبار الاتصال بقاعدة البيانات
```bash
php artisan tinker
```

```php
DB::connection()->getPdo();
// يجب أن يعرض معلومات الاتصال بدون أخطاء
```

### 2. اختبار الموقع
زيارة الموقع في المتصفح والتأكد من:
- تحميل الصفحة الرئيسية بشكل صحيح
- عمل نظام التسجيل والدخول
- ظهور المقررات

### 3. اختبار لوحة التحكم الإدارية
- تسجيل الدخول كمسؤول
- التأكد من عمل جميع الوظائف الإدارية

## إعداد نظام الجامعات

### 1. إضافة الجامعات الأساسية
```bash
php artisan db:seed --class=UniversitySeeder
```

### 2. ربط المقررات بالجامعات
```bash
php artisan db:seed --class=UniCourseSeeder
```

### 3. تحديث نظام التسعير
```bash
php artisan db:seed --class=UpdateCoursePricingSeeder
```

## إعداد المسؤول الأول

### إنشاء حساب المسؤول
```bash
php artisan tinker
```

```php
use App\Models\User;
use Illuminate\Support\Facades\Hash;

$admin = User::create([
    'name' => 'المسؤول الرئيسي',
    'email' => 'admin@yourdomain.com',
    'password' => Hash::make('secure_password'),
    'email_verified_at' => now(),
    'is_admin' => true,
]);
```

## النسخ الاحتياطي

### إعداد النسخ الاحتياطي التلقائي

#### نسخ احتياطي لقاعدة البيانات
```bash
#!/bin/bash
# backup-db.sh

DATE=$(date +%Y%m%d_%H%M%S)
BACKUP_DIR="/var/backups/himmah"
DB_NAME="himmah_platform"
DB_USER="himmah_user"
DB_PASS="your_password"

mkdir -p $BACKUP_DIR

mysqldump -u $DB_USER -p$DB_PASS $DB_NAME > $BACKUP_DIR/db_backup_$DATE.sql

# حذف النسخ الاحتياطية الأقدم من 30 يوم
find $BACKUP_DIR -name "db_backup_*.sql" -mtime +30 -delete
```

#### نسخ احتياطي للملفات
```bash
#!/bin/bash
# backup-files.sh

DATE=$(date +%Y%m%d_%H%M%S)
BACKUP_DIR="/var/backups/himmah"
PROJECT_DIR="/var/www/himmah-platform"

mkdir -p $BACKUP_DIR

tar -czf $BACKUP_DIR/files_backup_$DATE.tar.gz \
    $PROJECT_DIR/storage/app/public \
    $PROJECT_DIR/.env

# حذف النسخ الاحتياطية الأقدم من 30 يوم
find $BACKUP_DIR -name "files_backup_*.tar.gz" -mtime +30 -delete
```

#### جدولة النسخ الاحتياطي
```bash
crontab -e
```

إضافة:
```cron
# نسخ احتياطي يومي في الساعة 2:00 صباحاً
0 2 * * * /path/to/backup-db.sh
0 2 * * * /path/to/backup-files.sh
```

## المراقبة والصيانة

### مراقبة السجلات
```bash
# مراقبة سجلات Laravel
tail -f storage/logs/laravel.log

# مراقبة سجلات Apache
tail -f /var/log/apache2/himmah_error.log

# مراقبة سجلات Nginx
tail -f /var/log/nginx/error.log
```

### تنظيف الملفات المؤقتة
```bash
# تنظيف cache
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# تنظيف الجلسات المنتهية الصلاحية
php artisan session:gc
```

### تحديث النظام
```bash
# سحب آخر التحديثات
git pull origin main

# تحديث التبعيات
composer install --no-dev --optimize-autoloader
npm install && npm run build

# تشغيل الترحيلات الجديدة
php artisan migrate

# تنظيف وإعادة بناء cache
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

## استكشاف الأخطاء

### مشاكل شائعة

#### خطأ 500 - Internal Server Error
1. تحقق من سجلات الأخطاء
2. تأكد من صلاحيات الملفات
3. تحقق من إعدادات قاعدة البيانات

#### خطأ في الاتصال بقاعدة البيانات
1. تحقق من إعدادات .env
2. تأكد من تشغيل خدمة MySQL
3. تحقق من صلاحيات المستخدم

#### مشاكل في تحميل الملفات
1. تحقق من صلاحيات مجلد storage
2. تأكد من إنشاء الرابط الرمزي
3. تحقق من مساحة القرص الصلب

### أوامر مفيدة للتشخيص
```bash
# فحص حالة النظام
php artisan about

# فحص إعدادات البيئة
php artisan env

# اختبار الاتصال بقاعدة البيانات
php artisan migrate:status

# عرض المسارات المسجلة
php artisan route:list

# فحص الصلاحيات
ls -la storage/
ls -la bootstrap/cache/
```

## الأمان

### إعدادات الأمان الأساسية

#### تحديث كلمات المرور الافتراضية
- كلمة مرور قاعدة البيانات
- كلمة مرور المسؤول
- مفاتيح API

#### إعدادات الجدار الناري
```bash
# تفعيل UFW
sudo ufw enable

# السماح بـ SSH
sudo ufw allow ssh

# السماح بـ HTTP و HTTPS
sudo ufw allow 80
sudo ufw allow 443

# عرض حالة الجدار الناري
sudo ufw status
```

#### تحديث النظام بانتظام
```bash
sudo apt update && sudo apt upgrade -y
```

### مراقبة الأمان
- مراجعة سجلات الدخول بانتظام
- مراقبة محاولات الدخول المشبوهة
- تحديث النظام والتطبيقات بانتظام

## الدعم الفني

### في حالة وجود مشاكل
1. راجع سجلات الأخطاء أولاً
2. تأكد من اتباع جميع خطوات التثبيت
3. تحقق من متطلبات النظام
4. اتصل بفريق الدعم الفني

### معلومات الاتصال
- البريد الإلكتروني: support@himmah.edu.sa
- الهاتف: +966 XX XXX XXXX

---

**تم إعداد هذا الدليل بواسطة فريق تطوير منصة همة التعليمية**

