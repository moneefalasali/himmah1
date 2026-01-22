<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Purchase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use PaytabsApi\PaytabsApi;

class PaymentController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show payment form for course purchase.
     */
    public function showPaymentForm(Course $course)
    {
        $user = Auth::user();
        
        // Check if user already purchased this course
        if ($user->hasPurchased($course->id)) {
            return redirect()->route('courses.show', $course)
                ->with('info', 'لقد قمت بشراء هذه الدورة مسبقاً.');
        }

        return view('payment.form', compact('course'));
    }

    /**
     * Process payment for course purchase.
     */
    public function processCoursePayment(Request $request, Course $course)
    {
        $user = Auth::user();
        
        // Check if user already purchased this course
        if ($user->hasPurchased($course->id)) {
            return redirect()->route('courses.show', $course)
                ->with('info', 'لقد قمت بشراء هذه الدورة مسبقاً.');
        }

        // Create purchase record
        $purchase = Purchase::create([
            'user_id' => $user->id,
            'course_id' => $course->id,
            'amount' => $course->price,
            'payment_status' => 'pending',
            'payment_method' => 'paytabs',
        ]);

        try {
            // PayTabs configuration (should be in .env file)
            $paytabs = new PaytabsApi([
                'profile_id' => env('PAYTABS_PROFILE_ID'),
                'server_key' => env('PAYTABS_SERVER_KEY'),
                'base_url' => env('PAYTABS_BASE_URL', 'https://secure.paytabs.sa'),
            ]);

            // Prepare payment data
            $paymentData = [
                'tran_type' => 'sale',
                'tran_class' => 'ecom',
                'cart_id' => 'COURSE_' . $course->id . '_' . $purchase->id,
                'cart_description' => 'شراء دورة: ' . $course->title,
                'cart_currency' => 'SAR',
                'cart_amount' => $course->price,
                'callback' => route('payment.callback'),
                'return' => route('payment.return'),
                'customer_details' => [
                    'name' => $user->name,
                    'email' => $user->email,
                    'phone' => $user->phone ?? '',
                    'street1' => 'N/A',
                    'city' => 'Riyadh',
                    'state' => 'Riyadh',
                    'country' => 'SA',
                    'zip' => '12345',
                ],
                'shipping_details' => [
                    'name' => $user->name,
                    'email' => $user->email,
                    'phone' => $user->phone ?? '',
                    'street1' => 'N/A',
                    'city' => 'Riyadh',
                    'state' => 'Riyadh',
                    'country' => 'SA',
                    'zip' => '12345',
                ],
            ];

            // Create payment page
            $response = $paytabs->create_pay_page($paymentData);

            if (isset($response['redirect_url'])) {
                // Store transaction reference
                $purchase->update([
                    'transaction_id' => $response['tran_ref'] ?? null,
                ]);

                // Redirect to PayTabs payment page
                return redirect($response['redirect_url']);
            } else {
                throw new \Exception('فشل في إنشاء صفحة الدفع');
            }

        } catch (\Exception $e) {
            Log::error('Payment processing error: ' . $e->getMessage());
            
            $purchase->update(['payment_status' => 'failed']);
            
            return redirect()->route('courses.show', $course)
                ->with('error', 'حدث خطأ أثناء معالجة الدفع. يرجى المحاولة مرة أخرى.');
        }
    }

    /**
     * Handle payment callback from PayTabs.
     */
    public function paymentCallback(Request $request)
    {
        try {
            $tranRef = $request->input('tranRef');
            $cartId = $request->input('cartId');
            $respStatus = $request->input('respStatus');
            $respCode = $request->input('respCode');
            $respMessage = $request->input('respMessage');

            Log::info('PayTabs Callback', $request->all());

            // Extract purchase ID from cart_id
            if (preg_match('/COURSE_\d+_(\d+)/', $cartId, $matches)) {
                $purchaseId = $matches[1];
                $purchase = Purchase::find($purchaseId);

                if ($purchase) {
                    if ($respStatus === 'A' && $respCode === '100') {
                        // Payment successful
                        $purchase->update([
                            'payment_status' => 'completed',
                            'transaction_id' => $tranRef,
                        ]);
                        
                        Log::info('Payment completed for purchase: ' . $purchaseId);
                    } else {
                        // Payment failed
                        $purchase->update([
                            'payment_status' => 'failed',
                            'transaction_id' => $tranRef,
                        ]);
                        
                        Log::warning('Payment failed for purchase: ' . $purchaseId . ' - ' . $respMessage);
                    }
                }
            }

            return response('OK', 200);

        } catch (\Exception $e) {
            Log::error('Payment callback error: ' . $e->getMessage());
            return response('Error', 500);
        }
    }

    /**
     * Handle payment return from PayTabs.
     */
    public function paymentReturn(Request $request)
    {
        $tranRef = $request->input('tranRef');
        $cartId = $request->input('cartId');
        $respStatus = $request->input('respStatus');
        $respCode = $request->input('respCode');
        $respMessage = $request->input('respMessage');

        // Extract purchase ID from cart_id
        if (preg_match('/COURSE_\d+_(\d+)/', $cartId, $matches)) {
            $purchaseId = $matches[1];
            $purchase = Purchase::with('course')->find($purchaseId);

            if ($purchase) {
                if ($respStatus === 'A' && $respCode === '100') {
                    // Payment successful
                    return redirect()->route('courses.show', $purchase->course)
                        ->with('success', 'تم الدفع بنجاح! يمكنك الآن الوصول إلى جميع دروس الدورة.');
                } else {
                    // Payment failed
                    return redirect()->route('courses.show', $purchase->course)
                        ->with('error', 'فشل في عملية الدفع: ' . $respMessage);
                }
            }
        }

        return redirect()->route('courses.index')
            ->with('error', 'حدث خطأ أثناء معالجة الدفع.');
    }

    /**
     * Show payment history for user.
     */
    public function paymentHistory()
    {
        $user = Auth::user();
        $purchases = Purchase::with('course')
            ->where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('payment.history', compact('purchases'));
    }
}

