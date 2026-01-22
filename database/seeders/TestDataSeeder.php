<?php  

namespace Database\Seeders;  

use Illuminate\Database\Seeder;  
use Illuminate\Support\Facades\Log;
use App\Models\User;  
use App\Models\Course;  
use App\Models\Purchase;  
use App\Models\Review;  

class TestDataSeeder extends Seeder  
{  
    /**  
     * Run the database seeds.  
     */  
    public function run(): void  
    {  
        $this->createPurchases();  
        $this->createReviews(70); // نسبة إضافة المراجعات (تقدر تعدلها)
    }  

    private function createPurchases()  
    {  
        // قائمة المستخدمين والدورات
        $userCourses = [
           
            'asemalyrse@icloud.com' => ['اساسيات برمجة ++ c'],





        ];

        foreach ($userCourses as $email => $courses) {
            $user = User::where('email', $email)->first();

            if (!$user) {
                Log::warning("المستخدم '$email' غير موجود في جدول users.");
                continue;
            }

            foreach ($courses as $courseTitle) {
                $course = Course::where('title', $courseTitle)->first();

                if (!$course) {
                    Log::warning("الدورة '$courseTitle' غير موجودة في جدول courses.");
                    continue;
                }

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

    private function createReviews(int $percentage = 70)  
    {  
        $purchases = Purchase::where('payment_status', 'completed')->get();  

        foreach ($purchases as $purchase) {  
            // نسبة المراجعات حسب المتغير $percentage
            if (rand(1, 100) <= $percentage) {  
                $exists = Review::where('user_id', $purchase->user_id)
                    ->where('course_id', $purchase->course_id)
                    ->exists();

                if (!$exists) {
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
}  
