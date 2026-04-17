<!-- Sketo AI Chat Widget -->
<div id="sketo-ai-widget">
    <!-- Chat Icon / Launcher -->
    <button id="ai-chat-launcher" class="ai-launcher shadow-lg" title="Ask Sketo AI">
        <i class="la la-magic"></i>
    </button>

    <!-- Chat Panel -->
    <div id="ai-chat-panel" class="ai-panel shadow-sm">
        <div class="ai-header">
            <h5 class="mb-0 text-white font-weight-bold">
                <i class="la la-robot mr-2"></i> Sketo AI Advisor
            </h5>
            <button id="ai-chat-close" class="text-white"><i class="la la-times"></i></button>
        </div>
        
        <div class="ai-body" id="ai-chat-body">
            <!-- Messages go here -->
            <div class="ai-message ai-system">
                <div class="ai-bubble">Hello {{ Auth::user()->name }}! I am your AI Business Advisor. I can analyze your sales, check inventory, and give proactive tips. How can I help today?</div>
            </div>
        </div>

        <div class="ai-footer">
            <form id="ai-chat-form" class="d-flex w-100">
                <input type="text" id="ai-chat-input" class="form-control premium-ai-input" placeholder="Ask about today's sales..." autocomplete="off" required>
                <button type="submit" class="btn premium-ai-submit" id="ai-submit-btn">
                    <i class="la la-paper-plane"></i>
                </button>
            </form>
        </div>
    </div>
</div>

<style>
/* AI Chat Styles */
#sketo-ai-widget {
    position: fixed;
    bottom: 30px;
    right: 30px;
    z-index: 9999;
    font-family: 'Inter', 'Cairo', sans-serif;
}

.ai-launcher {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    background: linear-gradient(135deg, #10b981, #059669);
    color: white;
    font-size: 28px;
    border: none;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    box-shadow: 0 10px 25px rgba(16, 185, 129, 0.4) !important;
}

.ai-launcher:hover {
    transform: scale(1.1) translateY(-5px);
    box-shadow: 0 15px 35px rgba(16, 185, 129, 0.6) !important;
}

.ai-panel {
    position: fixed;
    bottom: 100px;
    right: 30px;
    width: 380px;
    height: 550px;
    background: rgba(15, 23, 42, 0.95);
    backdrop-filter: blur(20px);
    border: 1px solid rgba(255, 255, 255, 0.1);
    border-radius: 20px;
    display: flex;
    flex-direction: column;
    overflow: hidden;
    transform: translateY(50px);
    opacity: 0;
    pointer-events: none;
    transition: all 0.4s cubic-bezier(0.165, 0.84, 0.44, 1);
    box-shadow: 0 20px 50px rgba(0,0,0,0.5);
}

.ai-panel.open {
    transform: translateY(0);
    opacity: 1;
    pointer-events: all;
}

.ai-header {
    background: linear-gradient(90deg, #0f172a, #1e293b);
    padding: 20px;
    border-bottom: 1px solid rgba(255,255,255,0.05);
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.ai-header button {
    background: transparent;
    border: none;
    font-size: 20px;
    cursor: pointer;
    opacity: 0.7;
    transition: opacity 0.2s;
}
.ai-header button:hover { opacity: 1; }

.ai-body {
    flex: 1;
    padding: 20px;
    overflow-y: auto;
    display: flex;
    flex-direction: column;
    gap: 15px;
}

/* Custom Scrollbar for AI Body */
.ai-body::-webkit-scrollbar { width: 6px; }
.ai-body::-webkit-scrollbar-track { background: rgba(0,0,0,0.1); }
.ai-body::-webkit-scrollbar-thumb { background: rgba(16, 185, 129, 0.5); border-radius: 10px; }

.ai-message {
    display: flex;
    max-width: 85%;
    animation: slideUpFade 0.3s ease-out;
}

.ai-user {
    align-self: flex-end;
}

.ai-system {
    align-self: flex-start;
}

.ai-bubble {
    padding: 12px 16px;
    border-radius: 18px;
    font-size: 14px;
    line-height: 1.5;
    color: #f1f5f9;
}

.ai-user .ai-bubble {
    background: linear-gradient(135deg, #3b82f6, #2563eb);
    border-bottom-right-radius: 4px;
}

.ai-system .ai-bubble {
    background: rgba(30, 41, 59, 0.8);
    border: 1px solid rgba(255, 255, 255, 0.05);
    border-bottom-left-radius: 4px;
}

/* Markdown typography inside bubble */
.ai-system .ai-bubble p { margin-bottom: 10px; }
.ai-system .ai-bubble p:last-child { margin-bottom: 0; }
.ai-system .ai-bubble ul { padding-left: 20px; margin-bottom: 10px; }
.ai-system .ai-bubble strong { color: #10b981; }

.ai-footer {
    padding: 15px;
    background: rgba(15, 23, 42, 0.98);
    border-top: 1px solid rgba(255,255,255,0.05);
}

#ai-chat-input {
    background-color: rgba(30, 41, 59, 0.9) !important;
    border: 1px solid rgba(255,255,255,0.2) !important;
    color: #ffffff !important;
    border-radius: 30px !important;
    padding: 10px 20px !important;
}

#ai-chat-input::placeholder {
    color: rgba(255, 255, 255, 0.6) !important;
}

#ai-chat-input:focus {
    box-shadow: 0 0 0 2px rgba(16, 185, 129, 0.5) !important;
    border-color: #10b981 !important;
    background-color: rgba(15, 23, 42, 1) !important;
}

.premium-ai-submit {
    background: #10b981;
    color: white;
    border-radius: 50%;
    width: 42px;
    height: 42px;
    margin-left: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    border: none;
    transition: all 0.2s;
}

.premium-ai-submit:hover {
    background: #059669;
    transform: scale(1.05);
}

.ai-typing {
    display: inline-flex;
    gap: 4px;
    padding: 8px 12px;
    background: rgba(30, 41, 59, 0.8);
    border-radius: 18px;
    align-self: flex-start;
    display: none;
}

.ai-typing.active { display: inline-flex; }

.typing-dot {
    width: 6px;
    height: 6px;
    background: #94a3b8;
    border-radius: 50%;
    animation: typingBounce 1.4s infinite ease-in-out both;
}

.typing-dot:nth-child(1) { animation-delay: -0.32s; }
.typing-dot:nth-child(2) { animation-delay: -0.16s; }

@keyframes typingBounce {
    0%, 80%, 100% { transform: scale(0); }
    40% { transform: scale(1); }
}

@keyframes slideUpFade {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
}

/* RTL Support */
html[data-textdirection="rtl"] .ai-panel { right: auto; left: 30px; }
html[data-textdirection="rtl"] #sketo-ai-widget { right: auto; left: 30px; }
html[data-textdirection="rtl"] .premium-ai-submit { margin-left: 0; margin-right: 10px; }
html[data-textdirection="rtl"] .ai-system .ai-bubble { border-bottom-left-radius: 18px; border-bottom-right-radius: 4px; }
html[data-textdirection="rtl"] .ai-user .ai-bubble { border-bottom-right-radius: 18px; border-bottom-left-radius: 4px; }
</style>

<!-- Marked JS for parsing AI Markdown Responses -->
<script src="https://cdn.jsdelivr.net/npm/marked/marked.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const launcher = document.getElementById('ai-chat-launcher');
    const panel = document.getElementById('ai-chat-panel');
    const closeBtn = document.getElementById('ai-chat-close');
    const form = document.getElementById('ai-chat-form');
    const input = document.getElementById('ai-chat-input');
    const body = document.getElementById('ai-chat-body');
    const submitBtn = document.getElementById('ai-submit-btn');

    let chatHistory = [];

    // Toggle Chat Window
    launcher.addEventListener('click', () => {
        panel.classList.toggle('open');
        if (panel.classList.contains('open')) {
            input.focus();
            if (chatHistory.length === 0) {
                // Optional: Trigger initial proactive greeting check here
            }
        }
    });

    closeBtn.addEventListener('click', () => panel.classList.remove('open'));

    function createTypingIndicator() {
        const id = 'typing-' + Date.now();
        const html = `
            <div id="${id}" class="ai-message ai-system">
                <div class="ai-typing active">
                    <div class="typing-dot"></div>
                    <div class="typing-dot"></div>
                    <div class="typing-dot"></div>
                </div>
            </div>`;
        body.insertAdjacentHTML('beforeend', html);
        scrollToBottom();
        return id;
    }

    function appendMessage(role, content) {
        let displayContent = content;
        if (role === 'system') {
            displayContent = marked.parse(content); // Standard Markdown parsing
        }

        const msgClass = role === 'user' ? 'ai-user' : 'ai-system';
        const html = `
            <div class="ai-message ${msgClass}">
                <div class="ai-bubble">${displayContent}</div>
            </div>`;
        
        body.insertAdjacentHTML('beforeend', html);
        scrollToBottom();
    }

    function scrollToBottom() {
        body.scrollTop = body.scrollHeight;
    }

    form.addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const message = input.value.trim();
        if (!message) return;

        // Display user message
        appendMessage('user', message);
        input.value = '';
        input.disabled = true;
        submitBtn.disabled = true;

        // Show typing indicator
        const typingId = createTypingIndicator();

        try {
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
            
            const response = await fetch('{{ route('ai.chat') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken || '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    message: message,
                    history: chatHistory
                })
            });

            const data = await response.json();
            
            // Remove typing indicator
            document.getElementById(typingId).remove();

            if (data.status === 'success') {
                appendMessage('system', data.message);
                
                // Update internal history tracking
                chatHistory.push({ role: 'user', content: message });
                chatHistory.push({ role: 'assistant', content: data.message });
                
                // Keep history limited to last 10 messages to save tokens
                if (chatHistory.length > 10) {
                    chatHistory = chatHistory.slice(chatHistory.length - 10);
                }
            } else {
                appendMessage('system', '⚠ ' + data.message);
            }

        } catch (error) {
            console.error('AI Chat Error:', error);
            document.getElementById(typingId).remove();
            appendMessage('system', '⚠ Connection Error. Unable to reach Sketo AI Server.');
        } finally {
            input.disabled = false;
            submitBtn.disabled = false;
            input.focus();
        }
    });
});
</script>
