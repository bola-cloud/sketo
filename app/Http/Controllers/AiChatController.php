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
            'content' => "You are Sketo AI, an elite business advisor built directly into the SKETO Advanced POS System. Your role is to assist the shop owner with analyzing their sales, detecting low stock, and providing pro-active business advice. 
            Rules:
            1. Only use tools when the user specifically asks for data, reports, or business analysis.
            2. If the user just greets you (e.g., 'hi', 'hello', 'هاي'), respond with a friendly, professional greeting and ask how you can help them analyze their business today. Do NOT fetch data automatically for a simple greeting.
            3. You can engage in general conversation, but always maintain a professional business advisor persona.
            4. Output using markdown for beautiful structuring."
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
