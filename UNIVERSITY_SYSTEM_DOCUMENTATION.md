# نظام الجامعات - منصة همة التعليمية

## نظرة عامة

تم تطوير نظام الجامعات لدعم المقررات المخصصة لكل جامعة مع الحفاظ على المحتوى الموحد. يتيح هذا النظام للطلاب رؤية المقررات بترتيب مخصص حسب منهج جامعتهم، بينما يبقى المحتوى (الدروس، الملخصات، التجميعات) موحداً لجميع الجامعات.

## المميزات الرئيسية

### 1. إدارة الجامعات
- إضافة وتعديل وحذف الجامعات
- ربط الطلاب بجامعاتهم
- عرض إحصائيات لكل جامعة

### 2. المقررات المخصصة للجامعات
- ربط المقررات بالجامعات مع إمكانية تخصيص الأسماء
- ترتيب مخصص للدروس حسب منهج كل جامعة
- محتوى موحد (ملخصات وتجميعات) لجميع الجامعات

### 3. نظام التسعير المحدث
- تسعير فردي للمقررات
- مقررات عادية: 129-149 ريال
- مقررات موسعة: 149-179 ريال
- ملخصات وتجميعات مجانية مع كل مقرر

### 4. واجهة إدارية شاملة
- إدارة الجامعات والمقررات
- ترتيب الدروس بالسحب والإفلات
- إحصائيات مفصلة

## هيكل قاعدة البيانات

### الجداول الجديدة

#### 1. جدول `universities`
```sql
- id: المعرف الفريد
- name: اسم الجامعة
- city: المدينة (اختياري)
- created_at, updated_at: تواريخ الإنشاء والتحديث
```

#### 2. جدول `uni_courses`
```sql
- id: المعرف الفريد
- university_id: معرف الجامعة (مفتاح خارجي)
- course_id: معرف المقرر (مفتاح خارجي)
- custom_name: اسم مخصص للمقرر (اختياري)
- created_at, updated_at: تواريخ الإنشاء والتحديث
```

#### 3. جدول `course_lesson_mappings`
```sql
- id: المعرف الفريد
- uni_course_id: معرف مقرر الجامعة (مفتاح خارجي)
- lesson_id: معرف الدرس (مفتاح خارجي)
- order: ترتيب الدرس
- created_at, updated_at: تواريخ الإنشاء والتحديث
```

### التحديثات على الجداول الموجودة

#### جدول `users`
- إضافة `university_id`: معرف الجامعة (اختياري)

#### جدول `courses`
- إضافة `course_size`: حجم المقرر (normal/large)
- إضافة `includes_summary`: يشمل ملخص (boolean)
- إضافة `includes_tajmeeat`: يشمل تجميعات (boolean)

## التثبيت والإعداد

### 1. تشغيل الترحيلات (Migrations)
```bash
php artisan migrate
```

### 2. تشغيل البذور (Seeders)
```bash
php artisan db:seed --class=UniversitySeeder
php artisan db:seed --class=UniCourseSeeder
php artisan db:seed --class=UpdateCoursePricingSeeder
```

### 3. إعداد الصلاحيات
تأكد من أن المسؤولين لديهم صلاحية الوصول لإدارة الجامعات والمقررات.

## دليل الاستخدام

### للمسؤولين

#### إدارة الجامعات
1. الانتقال إلى لوحة التحكم الإدارية
2. اختيار "إدارة الجامعات" من القائمة
3. إضافة جامعة جديدة أو تعديل الموجودة

#### إدارة مقررات الجامعات
1. الانتقال إلى "إدارة مقررات الجامعات"
2. إضافة مقرر لجامعة معينة
3. تخصيص اسم المقرر (اختياري)
4. ترتيب الدروس حسب المنهج

#### ترتيب الدروس
1. الدخول على صفحة إدارة دروس المقرر
2. استخدام السحب والإفلات لإعادة ترتيب الدروس
3. حفظ الترتيب الجديد

### للطلاب

#### التسجيل
1. اختيار الجامعة عند التسجيل (اختياري)
2. إكمال بيانات التسجيل

#### عرض المقررات
- الطلاب المرتبطون بجامعة: يرون المقررات المخصصة لجامعتهم
- الطلاب غير المرتبطين: يرون جميع المقررات

#### دراسة المقررات
- ترتيب الدروس حسب منهج الجامعة
- الوصول للملخصات والتجميعات المجانية

## النماذج والعلاقات

### University Model
```php
// العلاقات
public function users() // المستخدمون
public function uniCourses() // مقررات الجامعة
public function courses() // المقررات (many-to-many)
```

### UniCourse Model
```php
// العلاقات
public function university() // الجامعة
public function course() // المقرر الأصلي
public function lessons() // الدروس (many-to-many)
public function courseLessonMappings() // خرائط الدروس

// الخصائص المحسوبة
public function getDisplayNameAttribute() // الاسم المعروض
public function getTotalLessonsAttribute() // عدد الدروس
```

### Course Model (المحدث)
```php
// الحقول الجديدة
protected $fillable = [
    // ... الحقول الموجودة
    'course_size',
    'includes_summary',
    'includes_tajmeeat',
];

// الطرق الجديدة
public function getSuggestedPriceAttribute() // السعر المقترح
public function isLarge() // هل المقرر موسع
public function getFeaturesDescriptionAttribute() // وصف المميزات
```

### User Model (المحدث)
```php
// العلاقة الجديدة
public function university() // الجامعة
```

## المسارات (Routes)

### مسارات الإدارة
```php
// إدارة الجامعات
Route::resource('admin/universities', AdminUniversityController::class);

// إدارة مقررات الجامعات
Route::resource('admin/uni_courses', AdminUniCourseController::class);
Route::get('admin/uni_courses/{uniCourse}/lessons', 'lessons');
Route::post('admin/uni_courses/{uniCourse}/lessons/order', 'updateLessonOrder');
Route::post('admin/uni_courses/{uniCourse}/lessons/add', 'addLesson');
Route::delete('admin/uni_courses/{uniCourse}/lessons/{mapping}', 'removeLesson');
```

## المتحكمات (Controllers)

### AdminUniversityController
- `index()`: عرض قائمة الجامعات
- `create()`: نموذج إضافة جامعة
- `store()`: حفظ جامعة جديدة
- `show()`: عرض تفاصيل الجامعة
- `edit()`: نموذج تعديل الجامعة
- `update()`: تحديث الجامعة
- `destroy()`: حذف الجامعة

### AdminUniCourseController
- `index()`: عرض قائمة مقررات الجامعات
- `create()`: نموذج إضافة مقرر جامعة
- `store()`: حفظ مقرر جامعة جديد
- `show()`: عرض تفاصيل مقرر الجامعة
- `edit()`: نموذج تعديل مقرر الجامعة
- `update()`: تحديث مقرر الجامعة
- `destroy()`: حذف مقرر الجامعة
- `lessons()`: إدارة دروس المقرر
- `updateLessonOrder()`: تحديث ترتيب الدروس
- `addLesson()`: إضافة درس للمقرر
- `removeLesson()`: حذف درس من المقرر

### CourseController (المحدث)
- `index()`: عرض المقررات (مع دعم الجامعات)
- `curriculum()`: عرض منهج المقرر (مع الترتيب المخصص)

## العروض (Views)

### عروض إدارة الجامعات
- `admin/universities/index.blade.php`: قائمة الجامعات
- `admin/universities/create.blade.php`: إضافة جامعة
- `admin/universities/edit.blade.php`: تعديل جامعة
- `admin/universities/show.blade.php`: تفاصيل الجامعة

### عروض إدارة مقررات الجامعات
- `admin/uni_courses/index.blade.php`: قائمة مقررات الجامعات
- `admin/uni_courses/create.blade.php`: إضافة مقرر جامعة
- `admin/uni_courses/edit.blade.php`: تعديل مقرر جامعة
- `admin/uni_courses/show.blade.php`: تفاصيل مقرر الجامعة
- `admin/uni_courses/lessons.blade.php`: إدارة دروس المقرر

### العروض المحدثة
- `courses/index.blade.php`: عرض المقررات (مع دعم الجامعات)
- `courses/curriculum.blade.php`: منهج المقرر (مع الترتيب المخصص)
- `courses/show.blade.php`: تفاصيل المقرر (مع التسعير الجديد)
- `auth/register.blade.php`: التسجيل (مع اختيار الجامعة)

## إضافة جامعة جديدة

### الطريقة الأولى: من لوحة التحكم
1. تسجيل الدخول كمسؤول
2. الانتقال إلى "إدارة الجامعات"
3. النقر على "إضافة جامعة جديدة"
4. ملء البيانات المطلوبة
5. حفظ الجامعة

### الطريقة الثانية: برمجياً
```php
use App\Models\University;

$university = University::create([
    'name' => 'اسم الجامعة',
    'city' => 'المدينة', // اختياري
]);
```

## إضافة مقرر لجامعة

### من لوحة التحكم
1. الانتقال إلى "إدارة مقررات الجامعات"
2. النقر على "إضافة مقرر جامعة"
3. اختيار الجامعة والمقرر
4. تحديد اسم مخصص (اختياري)
5. حفظ المقرر

### برمجياً
```php
use App\Models\UniCourse;
use App\Models\CourseLessonMapping;

// إنشاء مقرر الجامعة
$uniCourse = UniCourse::create([
    'university_id' => $universityId,
    'course_id' => $courseId,
    'custom_name' => 'اسم مخصص', // اختياري
]);

// ربط الدروس
$lessons = Lesson::where('course_id', $courseId)->orderBy('order')->get();
foreach ($lessons as $lesson) {
    CourseLessonMapping::create([
        'uni_course_id' => $uniCourse->id,
        'lesson_id' => $lesson->id,
        'order' => $lesson->order,
    ]);
}
```

## تخصيص ترتيب الدروس

### من لوحة التحكم
1. الانتقال إلى تفاصيل مقرر الجامعة
2. النقر على "إدارة الدروس"
3. استخدام السحب والإفلات لإعادة الترتيب
4. النقر على "حفظ الترتيب"

### برمجياً
```php
use App\Models\CourseLessonMapping;

// تحديث ترتيب الدروس
$mappings = [
    ['id' => 1, 'order' => 1],
    ['id' => 2, 'order' => 2],
    // ...
];

foreach ($mappings as $mapping) {
    CourseLessonMapping::where('id', $mapping['id'])
        ->update(['order' => $mapping['order']]);
}
```

## نظام التسعير

### أنواع المقررات
- **مقرر عادي**: 129-149 ريال سعودي
- **مقرر موسع**: 149-179 ريال سعودي

### المميزات المجانية
- ملخص شامل بصيغة PDF
- تجميعات أسئلة الاختبارات السابقة

### تحديد نوع المقرر
```php
// في نموذج المقرر
$course->course_size = 'large'; // أو 'normal'
$course->includes_summary = true;
$course->includes_tajmeeat = true;
$course->save();
```

## الأمان والصلاحيات

### صلاحيات المسؤولين
- إدارة الجامعات (إضافة، تعديل، حذف)
- إدارة مقررات الجامعات
- ترتيب الدروس
- عرض الإحصائيات

### صلاحيات الطلاب
- اختيار الجامعة عند التسجيل
- عرض المقررات المخصصة لجامعتهم
- الوصول للمحتوى المشترى

### الحماية
- جميع العمليات الإدارية محمية بـ middleware
- التحقق من صحة البيانات في جميع النماذج
- حماية من العمليات غير المصرح بها

## استكشاف الأخطاء

### مشاكل شائعة وحلولها

#### 1. عدم ظهور المقررات للطلاب
**السبب**: عدم ربط المقررات بالجامعة
**الحل**: 
```bash
php artisan db:seed --class=UniCourseSeeder
```

#### 2. ترتيب الدروس لا يعمل
**السبب**: عدم وجود خرائط الدروس
**الحل**: التأكد من إنشاء `CourseLessonMapping` لكل درس

#### 3. خطأ في الأسعار
**السبب**: عدم تحديث نظام التسعير
**الحل**:
```bash
php artisan db:seed --class=UpdateCoursePricingSeeder
```

### سجلات الأخطاء
تحقق من ملفات السجلات في:
```
storage/logs/laravel.log
```

## الصيانة والتحديث

### النسخ الاحتياطي
```bash
# نسخ احتياطي لقاعدة البيانات
mysqldump -u username -p database_name > backup.sql

# نسخ احتياطي للملفات
tar -czf files_backup.tar.gz storage/app/public/
```

### التحديثات المستقبلية
- إضافة المزيد من الجامعات
- تحسين واجهة إدارة الدروس
- إضافة تقارير مفصلة
- دعم اللغات المتعددة

## الدعم الفني

### معلومات الاتصال
- البريد الإلكتروني: support@himmah.edu.sa
- الهاتف: +966 XX XXX XXXX

### الموارد المفيدة
- [دليل Laravel الرسمي](https://laravel.com/docs)
- [دليل Bootstrap](https://getbootstrap.com/docs)
- [مستندات MySQL](https://dev.mysql.com/doc/)

---

**تم إنشاء هذا النظام بواسطة فريق تطوير منصة همة التعليمية**
**تاريخ آخر تحديث: أغسطس 2025**

