<?php  

namespace Database\Seeders;  

use Illuminate\Database\Console\Seeds\WithoutModelEvents;  
use Illuminate\Database\Seeder;  
use Illuminate\Support\Facades\DB;  
use App\Models\User;  
use App\Models\Course;  
use App\Models\Purchase;  
use App\Models\Review;  
use App\Models\ServiceRequest;  

class TestDataSeeder extends Seeder  
{  
    /**  
     * Run the database seeds.  
     */  
    public function run(): void  
    {  
        $this->createPurchases();  
        $this->createReviews();  
        $this->createServiceRequests();  
    }  

    private function createPurchases()  
    {  
        // قائمة المستخدمين المطلوبين
        $userEmails = [
            'alharremy078xi@gmail.com',
            'almmnyf@gmail.com',
        ];

        $courseTitle = 'مصطلحات طبية';

        $course = Course::where('title', $courseTitle)->first();

        if ($course) {
            foreach ($userEmails as $email) {
                $user = User::where('email', $email)->first();

                if ($user) {
                    // نتأكد أن الشراء ما يتكرر
                    $exists = Purchase::where('user_id', $user->id)
                        ->where('course_id', $course->id)
                        ->exists();

                    if (!$exists) {
                        Purchase::create([
                            'user_id' => $user->id,
                            'course_id' => $course->id,
                            'amount' => $course->price,
                            'payment_status' => 'completed',
                            'payment_method' => 'paytabs',
                            'transaction_id' => 'TXN_' . uniqid(),
                            'created_at' => now()->subDays(rand(1, 30)),
                            'updated_at' => now()->subDays(rand(1, 30)),
                        ]);
                    }
                }
            }
        }
    }  

    private function createReviews()  
    {  
        $purchases = Purchase::where('payment_status', 'completed')->get();  

        foreach ($purchases as $purchase) {  
            // 70% من المشتريات لها مراجعات  
            if (rand(1, 100) <= 70) {  
                Review::create([  
                    'user_id' => $purchase->user_id,  
                    'course_id' => $purchase->course_id,  
                    'rating' => rand(3, 5), // تقييم من 3-5 نجوم  
                    'comment' => $this->getRandomReviewComment(),  
                    'created_at' => $purchase->created_at->addDays(rand(1, 7)),  
                    'updated_at' => $purchase->created_at->addDays(rand(1, 7)),  
                ]);  
            }  
        }  
    }  

    private function createServiceRequests()  
    {  
        $users = User::where('role', 'user')->get();  
        $serviceTypes = DB::table('service_types')->get();  

        $statuses = ['pending', 'in_progress', 'completed', 'cancelled'];  
        $titles = [  
            'طلب حل واجب في الرياضيات',  
            'مشروع برمجة في Java',  
            'تحليل بيانات إحصائية',  
            'تصميم قاعدة بيانات',  
            'ترجمة مقال أكاديمي',  
            'تدقيق لغوي لرسالة ماجستير',  
            'إعداد عرض تقديمي',  
            'كتابة تقرير بحثي',  
            'تحليل مشكلة برمجية',  
            'تصميم واجهة مستخدم'  
        ];  

        $descriptions = [  
            'أحتاج مساعدة في حل مجموعة من المسائل الرياضية المتقدمة',  
            'مشروع برمجة يتطلب تطبيق كامل بلغة Java',  
            'تحليل بيانات إحصائية لمشروع بحثي',  
            'تصميم قاعدة بيانات لمتجر إلكتروني',  
            'ترجمة مقال أكاديمي من الإنجليزية إلى العربية',  
            'تدقيق لغوي وإملائي لرسالة ماجستير',  
            'إعداد عرض تقديمي احترافي',  
            'كتابة تقرير بحثي شامل',  
            'تحليل وحل مشكلة برمجية معقدة',  
            'تصميم واجهة مستخدم حديثة وسهلة الاستخدام'  
        ];  

        foreach ($users as $user) {  
            // كل مستخدم له 1-3 طلبات خدمات  
            $numRequests = rand(1, 3);  

            for ($i = 0; $i < $numRequests; $i++) {  
                $serviceType = $serviceTypes->random();  
                $status = $statuses[array_rand($statuses)];  
                $titleIndex = array_rand($titles);  

                ServiceRequest::create([  
                    'user_id' => $user->id,  
                    'service_type_id' => $serviceType->id,  
                    'title' => $titles[$titleIndex],  
                    'description' => $descriptions[$titleIndex],  
                    'requirements' => 'متطلبات إضافية: ' . $this->getRandomRequirements(),  
                    'status' => $status,  
                    'created_at' => now()->subDays(rand(1, 60)),  
                    'updated_at' => now()->subDays(rand(1, 60)),  
                ]);  
            }  
        }  
    }  

    private function getRandomReviewComment()  
    {  
        $comments = [  
            'دورة ممتازة ومفيدة جداً، أنصح بها بشدة',  
            'المحتوى واضح ومنظم، استفدت كثيراً',  
            'المدرب محترف ويشرح بطريقة سهلة',  
            'الدورة تغطي جميع المواضيع المطلوبة',  
            'محتوى عملي ومفيد للعمل',  
            'جودة عالية وأسعار معقولة',  
            'أنصح أي شخص يريد تعلم هذا المجال',  
            'دورة شاملة ومفصلة',  
            'المدرب متعاون ويجيب على جميع الأسئلة',  
            'محتوى حديث ومتطور'  
        ];  

        return $comments[array_rand($comments)];  
    }  

    private function getRandomRequirements()  
    {  
        $requirements = [  
            'يجب أن يكون الحل مفصل مع شرح الخطوات',  
            'مطلوب تسليم خلال 48 ساعة',  
            'يجب أن يكون العمل أصلي وغير منسوخ',  
            'مطلوب استخدام أحدث التقنيات',  
            'يجب أن يكون التصميم احترافي',  
            'مطلوب توثيق شامل للكود',  
            'يجب أن يكون التحليل دقيق ومفصل',  
            'مطلوب عرض تقديمي مع التقرير',  
            'يجب أن يكون العمل جاهز للعرض',  
            'مطلوب مراجعة وتدقيق شامل'  
        ];  

        return $requirements[array_rand($requirements)];  
    }  
}  
