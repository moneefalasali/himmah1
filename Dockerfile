# استخدام PHP 8.1 مع Apache
FROM php:8.1-apache

# تثبيت التبعيات المطلوبة
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    libzip-dev \
    libfreetype6-dev \
    libjpeg62-turbo-dev \
    libmcrypt-dev \
    libgd-dev \
    jpegoptim optipng pngquant gifsicle \
    vim \
    nano \
    nodejs \
    npm \
    ffmpeg

# تنظيف الكاش
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# تثبيت PHP extensions
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip

# تثبيت Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# تعيين مجلد العمل
WORKDIR /var/www/html

# نسخ ملفات المشروع
COPY . .

# تثبيت التبعيات
RUN composer install --optimize-autoloader --no-dev

# تثبيت Node.js dependencies وبناء الأصول
RUN npm install && npm run build

# تعيين الصلاحيات
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html/storage \
    && chmod -R 755 /var/www/html/bootstrap/cache

# تمكين Apache mod_rewrite
RUN a2enmod rewrite

# نسخ إعدادات Apache
COPY docker/apache.conf /etc/apache2/sites-available/000-default.conf

# تعرض البورت 80
EXPOSE 80

# تشغيل Apache
CMD ["apache2-foreground"]

