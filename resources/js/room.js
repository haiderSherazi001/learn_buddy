// 1. MUST IMPORT BOOTSTRAP FOR AXIOS & ECHO
import './bootstrap'; 

// 2. MUST WAIT FOR HTML TO LOAD
document.addEventListener('DOMContentLoaded', () => {
    
    const roomData = document.getElementById('room-data');
    
    if (roomData) {
        const roomId = roomData.dataset.roomId;
        const currentUserId = parseInt(roomData.dataset.userId);

        const chatContainer = document.getElementById('chat-messages');
        const chatForm = document.getElementById('chat-form');
        const chatInput = document.getElementById('chat-input');

        // 1. Auto-scroll to the bottom of the chat on page load
        chatContainer.scrollTop = chatContainer.scrollHeight;

        // 2. Listen for WebSockets from Reverb!
        Echo.private(`room.${roomId}`)
            .listen('MessageSent', (event) => {
                if (event.user.id !== currentUserId) {
                    appendMessage(event.message, event.user);
                }
            })
            .listen('RoomEventBroadcast', (e) => {
                const activityLog = document.getElementById('activity-log');
                const logData = e.event;
                
                const colorClass = logData.type === 'leave' 
                    ? 'border-red-400 text-red-700 bg-red-50' 
                    : 'border-blue-400 text-blue-700 bg-blue-50';

                // We just add our new custom 'animate-fade-in' class here!
                const html = `
                    <li class="text-sm border-l-2 pl-3 py-1 ${colorClass} rounded-r animate-fade-in">
                        <span class="block font-medium">${logData.message}</span>
                        <span class="text-xs opacity-75">Just now</span>
                    </li>
                `;
                
                activityLog.insertAdjacentHTML('afterbegin', html);
            });

        // 3. Hijack the form so we send messages via AJAX (no page reloads!)
        chatForm.addEventListener('submit', function (e) {
            e.preventDefault(); 
            const text = chatInput.value;
            if (!text.trim()) return;

            chatInput.value = ''; // Instantly clear the input box

            axios.post(chatForm.action, { body: text })
                 .then(response => {
                     // Draw our own message on the screen instantly
                     appendMessage(response.data.message, response.data.user);
                 })
                 .catch(error => {
                     console.error("Message failed to send:", error);
                 });
        });

        // Helper function to build the Chat Bubble HTML (ONLY ONE COPY NOW)
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
});