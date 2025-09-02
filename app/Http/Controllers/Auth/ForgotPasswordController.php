<?php





namespace App\Http\Controllers\Auth;

require_once base_path('vendor/autoload.php');
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\User;

class ForgotPasswordController extends Controller
{
    public function showLinkRequestForm()
    {
        return view('auth.passwords.email');
    }

    public function sendResetLinkEmail(Request $request)
    {
        $request->validate(['email' => 'required|email|exists:users,email']);
        $user = User::where('email', $request->email)->first();
        $token = Str::random(64);
        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $user->email],
            [
                'token' => Hash::make($token),
                'created_at' => now()
            ]
        );
        $resetUrl = url('password/reset/' . $token . '?email=' . urlencode($user->email));
        // إعداد PHPMailer
        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host = env('MAIL_HOST');
            $mail->SMTPAuth = true;
            $mail->Username = env('MAIL_USERNAME');
            $mail->Password = env('MAIL_PASSWORD');
            $mail->SMTPSecure = env('MAIL_ENCRYPTION', 'tls');
            $mail->Port = env('MAIL_PORT', 587);
            $mail->CharSet = 'UTF-8';
            $mail->setFrom(env('MAIL_FROM_ADDRESS'), env('MAIL_FROM_NAME', 'Himmah'));
            $mail->addAddress($user->email, $user->name);
            $mail->isHTML(true);
            $mail->Subject = 'رابط استعادة كلمة المرور';
            $mail->Body = 'مرحباً ' . $user->name . ',<br><br>اضغط على الرابط التالي لإعادة تعيين كلمة المرور:<br><a href="' . $resetUrl . '">' . $resetUrl . '</a><br><br>إذا لم تطلب ذلك، تجاهل هذه الرسالة.';
            $mail->send();
        } catch (Exception $e) {
            return back()->withErrors(['email' => 'حدث خطأ أثناء إرسال البريد: ' . $mail->ErrorInfo]);
        }
        return back()->with('status', 'تم إرسال رابط استعادة كلمة المرور إلى بريدك الإلكتروني.');
    }
}
