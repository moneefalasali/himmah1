<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ServiceTypesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $serviceTypes = [
            [
                'name' => 'حل واجب',
                'description' => 'حل الواجبات المدرسية والجامعية بدقة عالية مع شرح مفصل',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'مشروع تخرج',
                'description' => 'إرشاد ومساعدة في مشروع التخرج من البداية حتى النهاية',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'تدقيق لغوي',
                'description' => 'مراجعة أكاديمية وتدقيق لغوي لجميع أنواع المستندات',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'تحليل بيانات',
                'description' => 'تحليل البيانات باستخدام أدوات إحصائية متطورة',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'استشارة أكاديمية',
                'description' => 'استشارات في اختيار التخصص والمسار الأكاديمي',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'استفسار عام',
                'description' => 'استفسارات عامة أو طلب خدمة غير مدرجة',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'البحث الأكاديمي',
                'description' => 'خدمات البحث الأكاديمي والعلمي في مختلف المجالات',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'الملخصات',
                'description' => 'إعداد ملخصات للكتب والمقالات والأبحاث',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'التصميم التعليمي',
                'description' => 'تصميم المناهج والمواد التعليمية والعروض التقديمية',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'المشاريع البرمجية',
                'description' => 'تطوير المشاريع البرمجية والتطبيقات التعليمية',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'الترجمة',
                'description' => 'خدمات الترجمة الأكاديمية والتعليمية',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('service_types')->insert($serviceTypes);
    }
}
