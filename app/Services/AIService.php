<?php

namespace App\Services;

use OpenAI;
use Illuminate\Support\Facades\Log;
use App\Models\AIUsageLog;

class AIService
{
    protected $client;
    protected $model;
    protected $fallbackModels = ['gpt-3.5-turbo', 'gpt-4o-mini'];

    public function __construct()
    {
        $apiKey = config('services.openai.api_key');
        if (empty($apiKey)) {
            Log::error('OpenAI API key is not configured (services.openai.api_key). AI features will be disabled.');
            $this->client = null;
        } else {
            $this->client = OpenAI::client($apiKey);
        }
        $this->model = config('services.openai.model', 'gpt-4o-mini');
    }

    /**
     * إرسال طلب عام للذكاء الاصطناعي مع تسجيل الاستهلاك
     */
    public function ask($prompt, $systemMessage = "أنت مساعد ذكي في منصة تعليمية.", $userId = null, $context = [])
    {
        if (is_null($this->client)) {
            throw new \RuntimeException('OpenAI client not configured. Please set services.openai.api_key in your .env or config/services.php');
        }

        $modelsToTry = array_values(array_unique(array_merge([$this->model], $this->fallbackModels)));
        $response = null;
        $lastException = null;

        foreach ($modelsToTry as $model) {
            try {
                Log::info("Attempting AI request with model: {$model}");
                $response = $this->client->chat()->create([
                    'model' => $model,
                    'messages' => [
                        ['role' => 'system', 'content' => $systemMessage],
                        ['role' => 'user', 'content' => $prompt],
                    ],
                    'temperature' => 0.7,
                ]);
                $this->model = $model; // persist successful model
                break;
            } catch (\Throwable $e) {
                $lastException = $e;
                Log::warning("Model {$model} failed: " . $e->getMessage());
                // try next model
            }
        }

        if (is_null($response)) {
            // all attempts failed
            $msg = $lastException ? $lastException->getMessage() : 'Unknown error';
            Log::error("AI Service Error (all models failed): {$msg} -- " . ($lastException ? $lastException->getTraceAsString() : 'no-trace'));
            throw new \Exception("عذراً، حدث خطأ في معالجة طلبك عبر الذكاء الاصطناعي. تحقق من إعداد مفتاح OpenAI أو سجلات الخادم.");
        }

        // Safely extract answer and tokens depending on client response shape
        $answer = null;
        if (isset($response->choices[0]->message->content)) {
            $answer = $response->choices[0]->message->content;
        } elseif (isset($response->choices[0]->text)) {
            $answer = $response->choices[0]->text;
        }

        $tokens = $response->usage->total_tokens ?? ($response->usage->totalTokens ?? null);

        // تسجيل الاستهلاك
        if ($userId) {
            $this->logUsage($userId, $tokens, $context);
        }

        return $answer;
    }

    /**
     * تسجيل استهلاك التوكنات
     */
    protected function logUsage($userId, $tokens, $context)
    {
        AIUsageLog::create([
            'user_id' => $userId,
            'tokens_used' => $tokens,
            'feature' => $context['feature'] ?? 'general',
            'course_id' => $context['course_id'] ?? null,
            'metadata' => json_encode($context['metadata'] ?? []),
        ]);
    }
}
