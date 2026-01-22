<?php

namespace Database\Seeders;

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
                'slug' => 'hal-wajib',
                'description' => 'حل الواجبات المدرسية والجامعية بدقة عالية مع شرح مفصل',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'مشروع تخرج',
                'slug' => 'mashroa-takhrij',
                'description' => 'إرشاد ومساعدة في مشروع التخرج من البداية حتى النهاية',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'تدقيق لغوي',
                'slug' => 'tadqiq-lughwi',
                'description' => 'مراجعة أكاديمية وتدقيق لغوي لجميع أنواع المستندات',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'تحليل بيانات',
                'slug' => 'tahleel-bayanat',
                'description' => 'تحليل البيانات باستخدام أدوات إحصائية متطورة',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'استشارة أكاديمية',
                'slug' => 'istishara-akadimiya',
                'description' => 'استشارات في اختيار التخصص والمسار الأكاديمي',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'استفسار عام',
                'slug' => 'istifsar-aam',
                'description' => 'استفسارات عامة أو طلب خدمة غير مدرجة',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'البحث الأكاديمي',
                'slug' => 'al-bahth-al-akadimi',
                'description' => 'خدمات البحث الأكاديمي والعلمي في مختلف المجالات',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'الملخصات',
                'slug' => 'al-mulakhasat',
                'description' => 'إعداد ملخصات للكتب والمقالات والأبحاث',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'التصميم التعليمي',
                'slug' => 'tasmeem-taaleemi',
                'description' => 'تصميم المناهج والمواد التعليمية والعروض التقديمية',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'المشاريع البرمجية',
                'slug' => 'al-mashari3-al-barmajiyah',
                'description' => 'تطوير المشاريع البرمجية والتطبيقات التعليمية',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'الترجمة',
                'slug' => 'al-tarjama',
                'description' => 'خدمات الترجمة الأكاديمية والتعليمية',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('service_types')->insert($serviceTypes);
    }
}
