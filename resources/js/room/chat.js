export function initChatAndActivity(currentUserId, roomChannel) {
    const chatContainer = document.getElementById('chat-messages');
    const chatForm = document.getElementById('chat-form');
    const chatInput = document.getElementById('chat-input');
    const activityLog = document.getElementById('activity-log');

    if (!chatContainer || !chatForm) return;

    // Auto-scroll on page load
    chatContainer.scrollTop = chatContainer.scrollHeight;

    // Listen for WebSockets
    roomChannel
        .listen('MessageSent', (event) => {
            if (event.user.id !== currentUserId) {
                appendMessage(event.message, event.user);
            }
        })
        .listen('RoomEventBroadcast', (e) => {
            if (!activityLog) return;
            const logData = e.event;
            const colorClass = logData.type === 'leave' 
                ? 'border-red-400 text-red-700 bg-red-50' 
                : 'border-blue-400 text-blue-700 bg-blue-50';

            const html = `
                <li class="text-sm border-l-2 pl-3 py-1 ${colorClass} rounded-r animate-fade-in">
                    <span class="block font-medium">${logData.message}</span>
                    <span class="text-xs opacity-75">Just now</span>
                </li>
            `;
            activityLog.insertAdjacentHTML('afterbegin', html);

            const activityTab = document.getElementById('tab-activity');
            const badge = document.getElementById('activity-badge');
            
            if (activityTab && activityTab.classList.contains('hidden') && badge) {
                badge.classList.remove('hidden');
            }
        });

    // Chat form submit
    chatForm.addEventListener('submit', function (e) {
        e.preventDefault(); 
        const text = chatInput.value;
        if (!text.trim()) return;

        chatInput.value = ''; 

        axios.post(chatForm.action, { body: text })
             .then(response => appendMessage(response.data.message, response.data.user))
             .catch(error => console.error("Message failed to send:", error));
    });

    function appendMessage(message, user) {
        const isMe = user.id === currentUserId;
        const alignClass = isMe ? 'items-end' : 'items-start';
        const bubbleClass = isMe ? 'bg-green-600 text-white rounded-tr-sm' : 'bg-gray-100 text-gray-800 rounded-tl-sm';

        const html = `
            <div class="flex flex-col ${alignClass}">
                <span class="text-[10px] text-gray-400 mb-0.5 mx-1">${user.name}</span>
                <div class="px-3 py-2 text-sm shadow-sm max-w-[85%] break-words rounded-2xl ${bubbleClass}">
                    ${message.body}
                </div>
            </div>
        `;
        chatContainer.insertAdjacentHTML('beforeend', html);
        chatContainer.scrollTop = chatContainer.scrollHeight; 
    }
}