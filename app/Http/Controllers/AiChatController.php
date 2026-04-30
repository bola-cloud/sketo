<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\AiAgentService;
use Illuminate\Support\Facades\Log;

class AiChatController extends Controller
{
    public function __construct()
    {
        // Enforce auth
        $this->middleware('auth');
    }

    public function handleChat(Request $request, AiAgentService $aiAgent)
    {
        $request->validate([
            'message' => 'required|string',
            'history' => 'nullable|array',
            'proactive' => 'nullable|boolean',
        ]);

        $userMessage = $request->input('message');
        $history = $request->input('history', []);
        $isProactive = $request->input('proactive', false);

        $user = auth()->user();
        $vendorName = optional($user->vendor)->name ?? 'متجرنا';

        // Build System Prompt
        $systemPrompt = [
            'role' => 'system',
            'content' => "أنت Sketo AI، مساعد أعمال ذكي وخبير في نظام SKETO POS لمتجر ($vendorName). 
            قواعدك:
            1. لغة التواصل العربية الفصحى البسيطة والمهنية دائماً.
            2. استخدم Markdown في التنسيق وتجنب علامات الترقيم الإنجليزية في نهاية السطور.
            3. استخدم الأدوات فقط عند الحاجة لبيانات.
            4. ممنوع ذكر أسماء الأدوات التقنية (مثل get_sales_overview).
            5. إذا كانت مبيعات اليوم صفر، اقترح فحص فترة أطول بلباقة.
            6. كلمة 'من ساعة ما بدأت' تعني البحث في كل التاريخ (3650 يوم).
            7. لا تقلد الأخطاء السابقة في سجل المحادثة."
        ];

        $messages = [$systemPrompt];
        
        foreach ($history as $msg) {
            if (isset($msg['role']) && isset($msg['content'])) {
                $messages[] = [
                    'role' => $msg['role'] === 'user' ? 'user' : 'assistant',
                    'content' => (string)$msg['content']
                ];
            }
        }

        $messages[] = [
            'role' => 'user',
            'content' => $isProactive ? "أعطني ملخصاً تجارياً لليوم." : $userMessage
        ];

        try {
            $aiResponse = $aiAgent->ask($messages);
            
            $content = $aiResponse['content'] ?? 'عذراً، لم أستطع صياغة رد مناسب حالياً. هل يمكنك المحاولة مرة أخرى؟';

            return response()->json([
                'status' => 'success',
                'message' => $content,
            ]);
            
        } catch (\Exception $e) {
            Log::error('AiChatController Error: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'عذراً، واجهت مشكلة في الاتصال بالمساعد الذكي. يرجى المحاولة لاحقاً.'
            ], 500);
        }
    }
}
