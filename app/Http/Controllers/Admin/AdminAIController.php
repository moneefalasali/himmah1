<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\AIUsageLog;
use App\Services\AIService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class AdminAIController extends Controller
{
    protected $aiService;

    public function __construct(AIService $aiService)
    {
        $this->aiService = $aiService;
    }

    /**
     * توليد تقرير ذكي عن أداء المنصة
     */
    public function generatePlatformReport()
    {
        // تجميع بيانات إحصائية
        $stats = [
            'total_courses' => Course::count(),
            'total_ai_requests' => AIUsageLog::count(),
            'most_active_courses' => AIUsageLog::select('course_id', DB::raw('count(*) as total'))
                ->with('course:id,title')
                ->groupBy('course_id')
                ->orderBy('total', 'desc')
                ->limit(5)
                ->get(),
        ];

        $systemMessage = "أنت محلل بيانات خبير. قم بتحليل الإحصائيات التالية لمنصة تعليمية وقدم توصيات لتحسين الأداء وزيادة تفاعل الطلاب.";
        
        $prompt = json_encode($stats);

        $response = $this->aiService->ask(
            $prompt, 
            $systemMessage, 
            Auth::id(), 
            ['feature' => 'admin_report']
        );

        return view('admin.ai.report', compact('response', 'stats'));
    }

    /**
     * تنزيل التقرير كـ PDF (إن أمكن)
     */
    public function downloadPlatformReport()
    {
        // توليد نفس الإحصاءات
        $stats = [
            'total_courses' => Course::count(),
            'total_ai_requests' => AIUsageLog::count(),
            'most_active_courses' => AIUsageLog::select('course_id', DB::raw('count(*) as total'))
                ->with('course:id,title')
                ->groupBy('course_id')
                ->orderBy('total', 'desc')
                ->limit(5)
                ->get(),
        ];

        $systemMessage = "أنت محلل بيانات خبير. قم بتحليل الإحصائيات التالية لمنصة تعليمية وقدم توصيات لتحسين الأداء وزيادة تفاعل الطلاب.";
        $prompt = json_encode($stats);

        $response = $this->aiService->ask(
            $prompt,
            $systemMessage,
            Auth::id(),
            ['feature' => 'admin_report']
        );

        $view = view('admin.ai.report_pdf', compact('response', 'stats'))->render();

        // Try to generate PDF via barryvdh/laravel-dompdf if available
        try {
            if (class_exists(\Barryvdh\DomPDF\Facade\Pdf::class)) {
                $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadHTML($view)->setPaper('a4', 'portrait');
                return $pdf->download('platform_ai_report.pdf');
            }

            if (app()->bound('dompdf.wrapper')) {
                $pdf = app('dompdf.wrapper');
                $pdf->loadHTML($view);
                return $pdf->download('platform_ai_report.pdf');
            }
        } catch (\Throwable $e) {
            // log and fallback to HTML download
            \Illuminate\Support\Facades\Log::error('PDF generation failed: ' . $e->getMessage());
        }

        // Fallback: return the rendered HTML as a downloadable file
        return response($view, 200, [
            'Content-Type' => 'text/html; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="platform_ai_report.html"'
        ]);
    }

    /**
     * Legacy route handler compatibility: reports()
     */
    public function reports()
    {
        return $this->generatePlatformReport();
    }
}
