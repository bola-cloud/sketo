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
            'content' => "أنت Sketo AI، مساعد أعمال ذكي لمتجر ($vendorName). 
            قواعدك:
            1. لغة التواصل العربية الفصحى البسيطة والمهنية دائماً.
            2. استخدم Markdown في التنسيق وتجنب علامات الترقيم الإنجليزية في نهاية السطور.
            3. استخدم الأدوات فقط عند الحاجة لبيانات.
            4. **إضافة المنتجات**: إذا طلب المستخدم إضافة منتج جديد، استخدم (get_categories) أولاً لتعرف الأقسام المتاحة. إذا لم يذكر المستخدم (السعر، الكمية، أو القسم)، اسأله عنها أولاً قبل استخدام أداة (create_product).
            5. ممنوع ذكر أسماء الأدوات التقنية (مثل get_sales_overview).
            6. إذا كانت مبيعات اليوم صفر، اقترح فحص فترة أطول بلباقة.
            7. كلمة 'من ساعة ما بدأت' تعني البحث في كل التاريخ (3650 يوم).
            8. لا تقلد الأخطاء السابقة في سجل المحادثة."
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
