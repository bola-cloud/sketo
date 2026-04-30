<!-- Sketo AI Chat Widget -->
<div id="sketo-ai-widget">
    <!-- Chat Icon / Launcher -->
    <button id="ai-chat-launcher" class="ai-launcher shadow-lg" title="Ask Sketo AI">
        <i class="la la-magic"></i>
    </button>

    <!-- Chat Panel -->
    <div id="ai-chat-panel" class="ai-panel shadow-sm">
        <!-- Sidebar for History -->
        <div id="ai-history-sidebar" class="ai-sidebar">
            <div class="sidebar-header">
                <span>{{ __('app.ai.history') ?? 'المحادثات' }}</span>
                <button id="new-chat-btn" class="btn btn-sm btn-success rounded-circle" title="New Chat">
                    <i class="la la-plus"></i>
                </button>
            </div>
            <div id="ai-chat-list" class="chat-list">
                <!-- Chat list items will be injected here -->
                <div class="text-center p-3 opacity-50 small">جاري تحميل التاريخ...</div>
            </div>
        </div>

        <!-- Main Chat Area -->
        <div class="ai-main-chat">
            <div class="ai-header">
                <div class="d-flex align-items-center">
                    <button id="toggle-history" class="mr-2"><i class="la la-history"></i></button>
                    <h5 class="mb-0 text-white font-weight-bold" style="font-size: 14px;">
                        <i class="la la-robot"></i> Sketo AI Advisor
                    </h5>
                </div>
                <button id="ai-chat-close"><i class="la la-times"></i></button>
            </div>
            
            <div class="ai-body" id="ai-chat-body">
                <!-- Messages go here -->
                <div class="ai-message ai-system">
                    <div class="ai-bubble">مرحباً {{ Auth::user()->name }}! أنا مساعدك الذكي. كيف يمكنني مساعدتك في تحليل تجارتك اليوم؟</div>
                </div>
            </div>

            <div class="ai-footer">
                <form id="ai-chat-form" class="d-flex w-100">
                    <input type="text" id="ai-chat-input" class="form-control premium-ai-input" placeholder="اسأل عن مبيعاتك أو مخزونك..." autocomplete="off" required>
                    <button type="submit" class="btn premium-ai-submit" id="ai-submit-btn">
                        <i class="la la-paper-plane"></i>
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
/* AI Chat Styles - Optimized for Premium Look */
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

.ai-panel {
    position: fixed;
    bottom: 100px;
    right: 30px;
    width: 400px;
    height: 600px;
    background: rgba(15, 23, 42, 0.95);
    backdrop-filter: blur(20px);
    border: 1px solid rgba(255, 255, 255, 0.1);
    border-radius: 24px;
    display: flex;
    overflow: hidden;
    transform: translateY(50px);
    opacity: 0;
    pointer-events: none;
    transition: all 0.4s cubic-bezier(0.165, 0.84, 0.44, 1);
    box-shadow: 0 25px 60px rgba(0,0,0,0.6);
}

.ai-panel.open { transform: translateY(0); opacity: 1; pointer-events: all; }

/* Sidebar Styles */
.ai-sidebar {
    width: 0;
    background: rgba(30, 41, 59, 0.5);
    border-right: 1px solid rgba(255,255,255,0.05);
    overflow: hidden;
    transition: width 0.3s ease;
    display: flex;
    flex-direction: column;
}

.ai-sidebar.active { width: 200px; }

.sidebar-header {
    padding: 15px;
    font-size: 13px;
    color: #94a3b8;
    display: flex;
    justify-content: space-between;
    align-items: center;
    border-bottom: 1px solid rgba(255,255,255,0.05);
}

.chat-list { flex: 1; overflow-y: auto; padding: 10px; }

.chat-item {
    padding: 10px;
    border-radius: 10px;
    font-size: 12px;
    color: #e2e8f0;
    cursor: pointer;
    margin-bottom: 5px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    transition: background 0.2s;
    border: 1px solid transparent;
}

.chat-item:hover { background: rgba(16, 185, 129, 0.1); border-color: rgba(16, 185, 129, 0.2); }
.chat-item.active { background: rgba(16, 185, 129, 0.2); border-color: #10b981; }

/* Main Chat Area */
.ai-main-chat { flex: 1; display: flex; flex-direction: column; overflow: hidden; }

.ai-header {
    background: rgba(15, 23, 42, 0.6);
    padding: 15px 20px;
    border-bottom: 1px solid rgba(255,255,255,0.05);
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.ai-header button { background: transparent; border: none; color: white; cursor: pointer; opacity: 0.7; }
.ai-header button:hover { opacity: 1; }

.ai-body { flex: 1; padding: 20px; overflow-y: auto; display: flex; flex-direction: column; gap: 15px; }
.ai-body::-webkit-scrollbar { width: 4px; }
.ai-body::-webkit-scrollbar-thumb { background: rgba(16, 185, 129, 0.3); border-radius: 10px; }

.ai-message { display: flex; max-width: 85%; animation: slideUpFade 0.3s ease-out; }
.ai-user { align-self: flex-end; }
.ai-system { align-self: flex-start; }

.ai-bubble { padding: 12px 16px; border-radius: 20px; font-size: 13.5px; line-height: 1.6; color: #f1f5f9; position: relative; }
.ai-user .ai-bubble { background: linear-gradient(135deg, #10b981, #059669); border-bottom-right-radius: 4px; box-shadow: 0 4px 15px rgba(16, 185, 129, 0.2); }
.ai-system .ai-bubble { background: rgba(30, 41, 59, 0.8); border: 1px solid rgba(255, 255, 255, 0.1); border-bottom-left-radius: 4px; }

.ai-footer { padding: 15px; background: rgba(15, 23, 42, 0.8); border-top: 1px solid rgba(255,255,255,0.05); }

#ai-chat-input {
    background-color: rgba(30, 41, 59, 0.9) !important;
    border: 1px solid rgba(255,255,255,0.1) !important;
    color: #ffffff !important;
    border-radius: 30px !important;
    padding: 10px 20px !important;
    font-size: 13px;
}

.premium-ai-submit {
    background: #10b981;
    color: white;
    border-radius: 50%;
    width: 40px;
    height: 40px;
    margin-left: 10px;
    border: none;
    transition: all 0.2s;
}

.ai-typing { display: inline-flex; gap: 4px; padding: 8px 12px; background: rgba(30, 41, 59, 0.8); border-radius: 18px; align-self: flex-start; display: none; }
.ai-typing.active { display: inline-flex; }
.typing-dot { width: 6px; height: 6px; background: #10b981; border-radius: 50%; animation: typingBounce 1.4s infinite ease-in-out both; }
.typing-dot:nth-child(1) { animation-delay: -0.32s; }
.typing-dot:nth-child(2) { animation-delay: -0.16s; }

@keyframes typingBounce { 0%, 80%, 100% { transform: scale(0); } 40% { transform: scale(1); } }
@keyframes slideUpFade { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }

/* RTL Support - Fixed for Sidebar */
html[data-textdirection="rtl"] .ai-panel { right: auto; left: 30px; flex-direction: row-reverse; }
html[data-textdirection="rtl"] .ai-sidebar { border-right: none; border-left: 1px solid rgba(255,255,255,0.05); }
html[data-textdirection="rtl"] .premium-ai-submit { margin-left: 0; margin-right: 10px; transform: scaleX(-1); }
html[data-textdirection="rtl"] #sketo-ai-widget { right: auto; left: 30px; }
</style>

<script src="https://cdn.jsdelivr.net/npm/marked/marked.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const launcher = document.getElementById('ai-chat-launcher');
    const panel = document.getElementById('ai-chat-panel');
    const sidebar = document.getElementById('ai-history-sidebar');
    const toggleHistory = document.getElementById('toggle-history');
    const newChatBtn = document.getElementById('new-chat-btn');
    const chatList = document.getElementById('ai-chat-list');
    const body = document.getElementById('ai-chat-body');
    const form = document.getElementById('ai-chat-form');
    const input = document.getElementById('ai-chat-input');
    const submitBtn = document.getElementById('ai-submit-btn');

    let currentChatId = null;

    // Toggle Chat Window
    launcher.addEventListener('click', () => {
        panel.classList.toggle('open');
        if (panel.classList.contains('open')) {
            loadChatHistory();
        }
    });

    document.getElementById('ai-chat-close').addEventListener('click', () => panel.classList.remove('open'));

    // Toggle Sidebar
    toggleHistory.addEventListener('click', () => sidebar.classList.toggle('active'));

    // New Chat
    newChatBtn.addEventListener('click', () => {
        currentChatId = null;
        body.innerHTML = `
            <div class="ai-message ai-system">
                <div class="ai-bubble">بدأنا محادثة جديدة. كيف يمكنني مساعدتك الآن؟</div>
            </div>`;
        sidebar.classList.remove('active');
        const activeItems = document.querySelectorAll('.chat-item.active');
        activeItems.forEach(item => item.classList.remove('active'));
    });

    async function loadChatHistory() {
        try {
            const response = await fetch('{{ route('ai.chats') }}');
            const data = await response.json();
            if (data.status === 'success') {
                chatList.innerHTML = '';
                data.chats.forEach(chat => {
                    const div = document.createElement('div');
                    div.className = 'chat-item';
                    if (chat.id === currentChatId) div.classList.add('active');
                    div.innerHTML = `<i class="la la-comment mr-1"></i> ${chat.title}`;
                    div.onclick = () => selectChat(chat.id);
                    chatList.appendChild(div);
                });
            }
        } catch (e) { console.error('History load failed'); }
    }

    async function selectChat(chatId) {
        currentChatId = chatId;
        sidebar.classList.remove('active');
        body.innerHTML = '<div class="text-center p-5 opacity-50"><i class="la la-spinner la-spin"></i> جاري التحميل...</div>';
        
        try {
            const response = await fetch(`/ai/chats/${chatId}/messages`);
            const data = await response.json();
            if (data.status === 'success') {
                body.innerHTML = '';
                data.messages.forEach(msg => appendMessage(msg.role, msg.content));
            }
        } catch (e) { appendMessage('system', 'خطأ في تحميل المحادثة'); }
    }

    function createTypingIndicator() {
        const id = 'typing-' + Date.now();
        body.insertAdjacentHTML('beforeend', `
            <div id="${id}" class="ai-message ai-system">
                <div class="ai-typing active"><div class="typing-dot"></div><div class="typing-dot"></div><div class="typing-dot"></div></div>
            </div>`);
        body.scrollTop = body.scrollHeight;
        return id;
    }

    function appendMessage(role, content) {
        const isSystem = role === 'assistant' || role === 'system';
        const displayContent = isSystem ? marked.parse(content) : content;
        const html = `
            <div class="ai-message ${isSystem ? 'ai-system' : 'ai-user'}">
                <div class="ai-bubble">${displayContent}</div>
            </div>`;
        body.insertAdjacentHTML('beforeend', html);
        body.scrollTop = body.scrollHeight;
    }

    form.addEventListener('submit', async function(e) {
        e.preventDefault();
        const message = input.value.trim();
        if (!message) return;

        appendMessage('user', message);
        input.value = '';
        const typingId = createTypingIndicator();

        try {
            const response = await fetch('{{ route('ai.chat') }}', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                body: JSON.stringify({ message, chat_id: currentChatId })
            });
            const data = await response.json();
            document.getElementById(typingId).remove();
            if (data.status === 'success') {
                appendMessage('assistant', data.message);
                if (!currentChatId) {
                    currentChatId = data.chat_id;
                    loadChatHistory();
                }
            }
        } catch (error) {
            document.getElementById(typingId).remove();
            appendMessage('system', '⚠ حدث خطأ في الاتصال بالسيرفر');
        }
    });
});
</script>
