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
            'proactive' => 'nullable|boolean', // Used if we just want a daily summary
        ]);

        $userMessage = $request->input('message');
        $history = $request->input('history', []);
        $isProactive = $request->input('proactive', false);

        // Build System Prompt
        $systemPrompt = [
            'role' => 'system',
            'content' => "أنت Sketo AI، مساعد أعمال ذكي وخبير في نظام SKETO POS. دورك هو مساعدة صاحب المتجر في تحليل المبيعات، ومراقبة المخزون، وتقديم نصائح تجارية احترافية.
            
            قواعد هامة:
            1. لغة التواصل: يجب أن تكون جميع ردودك باللغة العربية الفصحى البسيطة والمهنية، إلا إذا طلب المستخدم لغة أخرى.
            2. تنسيق النصوص: استخدم Markdown لتنسيق الردود بشكل جميل. تجنب وضع علامات الترقيم الإنجليزية في نهاية السطور العربية لتجنب مشاكل العرض (RTL).
            3. استخدام الأدوات: استخدم الأدوات فقط عندما يطلب المستخدم بيانات محددة. إذا سأل المستخدم عن المبيعات ولم يحدد تاريخاً، فافترض أنه يقصد 'اليوم'.
            4. شفافية البيانات: إذا كانت مبيعات 'اليوم' صفر، وضح للمستخدم أنك تفحص بيانات اليوم الحالي (بتاريخ اليوم) واقترح عليه فحص فترة أطول (أسبوع مثلاً) باستخدام أدواتك.
            5. التفاعل: إذا قام المستخدم بتحيتك، رد بترحيب مهني واسأله كيف يمكنك مساعدته في تحليل تجارته اليوم.
            
            معلومات إضافية:
            - العملة المستخدمة: ج.م (جنيه مصري).
            - اسم النظام: SKETO POS."
        ];

        // Format conversation history for OpenAI chat format
        $messages = [$systemPrompt];
        
        foreach ($history as $msg) {
            if (isset($msg['role']) && isset($msg['content'])) {
                $messages[] = [
                    'role' => $msg['role'] === 'user' ? 'user' : 'assistant',
                    'content' => $msg['content']
                ];
            }
        }

        // Add the current message
        $messages[] = [
            'role' => 'user',
            'content' => $isProactive ? "Please give me a quick proactive business brief for today. If numbers are good, encourage me. If items are low, warn me." : $userMessage
        ];

        try {
            // Call the AI Service (this handles tool calling natively)
            $aiResponse = $aiAgent->ask($messages);
            
            return response()->json([
                'status' => 'success',
                'message' => $aiResponse['content'],
            ]);
            
        } catch (\Exception $e) {
            Log::error('AiChatController Error: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'I am currently offline or experiencing a connection issue. Please check your AI API configurations in .env or the proxy server.'
            ], 500);
        }
    }
}
