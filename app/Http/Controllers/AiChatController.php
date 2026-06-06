<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\AiAgentService;
use App\Models\AiChat;
use App\Models\AiMessage;
use Illuminate\Support\Facades\Log;

class AiChatController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Get list of all chats for the logged in vendor
     */
    public function getChats()
    {
        $chats = AiChat::where('vendor_id', auth()->user()->vendor_id)
            ->where('user_id', auth()->id())
            ->orderBy('updated_at', 'desc')
            ->get();
            
        return response()->json(['status' => 'success', 'chats' => $chats]);
    }

    /**
     * Get messages for a specific chat
     */
    public function getMessages($chatId)
    {
        $chat = AiChat::where('vendor_id', auth()->user()->vendor_id)
            ->where('user_id', auth()->id())
            ->findOrFail($chatId);
            
        $messages = $chat->messages()->orderBy('created_at', 'asc')->get();
        
        return response()->json(['status' => 'success', 'messages' => $messages]);
    }

    /**
     * Handle chat interaction
     */
    public function handleChat(Request $request, AiAgentService $aiAgent)
    {
        $request->validate([
            'message' => 'required|string',
            'chat_id' => 'nullable|integer',
            'proactive' => 'nullable|boolean',
        ]);

        $userMessage = $request->input('message');
        $chatId = $request->input('chat_id');
        $isProactive = $request->input('proactive', false);

        $user = auth()->user();
        $vendorName = optional($user->vendor)->name ?? 'متجرنا';

        // 1. Find or Create Chat
        if ($chatId) {
            $chat = AiChat::where('vendor_id', $user->vendor_id)
                ->where('user_id', $user->id)
                ->findOrFail($chatId);
        } else {
            $chat = AiChat::create([
                'vendor_id' => $user->vendor_id,
                'user_id' => $user->id,
                'title' => mb_substr($userMessage, 0, 30) . '...'
            ]);
        }

        // 2. Build System Prompt
        $systemPrompt = [
            'role' => 'system',
            'content' => "أنت مستشار أعمال Sketo AI الخاص بمتجر ($vendorName). 
            قواعدك الأساسية:
            1. تواصل باللغة العربية الفصحى البسيطة والودية، كأنك مستشار مالي وتجاري.
            2. **هام جداً:** لا تذكر أبداً أسماء الدوال البرمجية أو الأدوات التقنية (مثل get_low_stock_products أو get_financial_dues).
            3. إذا أردت معرفة معلومة (مثل المبيعات، النواقص، أو المديونيات)، قم باستدعاء الأداة (Function Call) في الخلفية مباشرة دون إخبار المستخدم.
            4. استخدم تنسيق Markdown بذكاء لتوضيح النقاط والأرقام (مثل الجداول لعرض المديونيات والمستحقات والفواتير، والقوائم النقطية).
            5. عند الاستعلام عن تواريخ انتهاء الصلاحية عبر الأداة، احسب ووضح للعميل 'قيمة المنتجات التي ستخسرها إذا انتهت صلاحيتها' و 'الربح المحتمل إذا تم بيعها سريعاً' واقترح عليه عمل عروض خصم عليها.
            6. عند الاستعلام عن المديونيات والمستحقات، استخدم جدولاً لعرض المبالغ المستحقة لكل مورد وكل عميل وتواريخها، ووضح الإجمالي.
            7. قدم دائماً نصائح استباقية لتحسين المبيعات أو تقليل التوالف بناءً على البيانات.
            8. **العملة:** استخدم الجنيه المصري (ج.م) فقط في كل المبالغ المالية. يمنع منعاً باتاً استخدام الريال، الدرهم، أو أي عملة أخرى.
            9. **التاريخ:** استخدم التاريخ الميلادي فقط (مثل 6 يونيو 2025). يمنع منعاً باتاً استخدام التاريخ الهجري أو ذكره.
            10. **الأخطاء التقنية:** إذا رجعت لك الأداة بخطأ (error) أو لم تجد مبيعات اليوم، إياك أن تعتذر عن 'تعطل في النظام' أو 'مشكلة تقنية'. فقط قل للمستخدم بلباقة 'لا توجد بيانات مسجلة حالياً' أو 'لم يتم تسجيل مبيعات اليوم حتى الآن'، ولا تذكر أن هناك عطلاً أبداً.
            11. **الأرقام العشرية:** كمية المبيعات قد تكون رقماً عشرياً (مثل 3.2 أو 1.5). هذا طبيعي 100% للمنتجات التي تباع بالوزن. تعامل معها كأرقام صحيحة وصالحة.
            12. **الثقة:** لا تقل أبداً 'لا أستطيع الوصول للتقرير'. قدم المعلومات المتاحة بثقة تامة كأنك استخرجتها للتو."
        ];

        // 3. Build message history from database
        $messages = [$systemPrompt];
        $dbMessages = $chat->messages()->orderBy('created_at', 'asc')->get();
        
        foreach ($dbMessages as $dbMsg) {
            $messages[] = [
                'role' => $dbMsg->role,
                'content' => $dbMsg->content
            ];
        }

        // 4. Add current user message to DB and conversation
        $contentToSend = $isProactive ? "أعطني ملخصاً تجارياً لليوم." : $userMessage;
        
        AiMessage::create([
            'chat_id' => $chat->id,
            'role' => 'user',
            'content' => $contentToSend
        ]);

        $messages[] = [
            'role' => 'user',
            'content' => $contentToSend
        ];

        try {
            // 5. Call AI
            $aiResponse = $aiAgent->ask($messages);
            $content = $aiResponse['content'] ?? 'عذراً، لم أستطع الرد حالياً.';

            // 6. Save Assistant response to DB
            AiMessage::create([
                'chat_id' => $chat->id,
                'role' => 'assistant',
                'content' => $content
            ]);

            // Update chat timestamp
            $chat->touch();

            return response()->json([
                'status' => 'success',
                'chat_id' => $chat->id,
                'message' => $content,
            ]);
            
        } catch (\Exception $e) {
            Log::error('AiChatController Error: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'عذراً، واجهت مشكلة في الاتصال بالمساعد الذكي.'
            ], 500);
        }
    }
}
