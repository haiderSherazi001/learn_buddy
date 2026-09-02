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

        // --- COMMITMENTS LOGIC ---
        const commitmentForm = document.getElementById('commitment-form');
        const commitmentInput = document.getElementById('commitment-input');
        const commitmentsList = document.getElementById('commitments-list');

        if (commitmentForm) {
            // 1. Listen for other users creating goals
            Echo.private(`room.${roomId}`)
                .listen('CommitmentCreated', (event) => {
                    if (event.commitment.user_id !== currentUserId) {
                        appendCommitment(event.commitment);
                    }
                })
                // ⚡ NEW: Listen for users completing their goals!
                .listen('CommitmentUpdated', (event) => {
                    if (event.commitment.is_completed) {
                        markCommitmentAsDone(event.commitment.id);
                    }
                });

            // 2. Hijack the form to send via AJAX (Creating goals)
            commitmentForm.addEventListener('submit', function (e) {
                e.preventDefault();
                const goalText = commitmentInput.value;
                if (!goalText.trim()) return;

                commitmentInput.value = ''; // Clear input instantly

                axios.post(commitmentForm.action, { goal: goalText })
                     .then(response => {
                         // Draw our own goal on the screen
                         appendCommitment(response.data.commitment);
                     })
                     .catch(error => console.error(error));
            });

            // ⚡ NEW: Handle clicking "Mark Done" via Event Delegation
            commitmentsList.addEventListener('submit', function (e) {
                if (e.target.matches('form.toggle-commitment-form')) {
                    e.preventDefault();
                    const form = e.target;
                    const commitmentId = form.dataset.id;

                    // Instantly update our own UI
                    markCommitmentAsDone(commitmentId);

                    // Send the request in the background
                    axios.post(form.action, { _method: 'PATCH' })
                         .catch(error => console.error(error));
                }
            });
        }

        // 3. Helper function to draw the HTML for a new commitment
        function appendCommitment(commitment) {
            // Remove the "No commitments set yet" text if it exists
            const emptyText = commitmentsList.querySelector('p.italic');
            if (emptyText) emptyText.remove();

            const isMe = commitment.user_id === currentUserId;
            
            // Only build the "Mark Done" button if the current user owns this goal
            const actionButtonHTML = isMe 
                ? `<form action="/commitments/${commitment.id}/toggle" method="POST" class="toggle-commitment-form" data-id="${commitment.id}">
                       <input type="hidden" name="_token" value="${document.querySelector('meta[name="csrf-token"]').content}">
                       <input type="hidden" name="_method" value="PATCH">
                       <button type="submit" class="text-xs px-3 py-1 rounded font-bold border transition bg-green-50 text-green-700 border-green-200 hover:bg-green-100">
                           Mark Done ✔
                       </button>
                   </form>`
                : ``;

            const html = `
                <div class="commitment-item p-4 border rounded-lg shadow-sm transition bg-white border-gray-300 animate-fade-in" data-id="${commitment.id}">
                    <div class="flex justify-between items-start">
                        <div>
                            <div class="flex items-center space-x-2 mb-2">
                                <span class="font-bold text-gray-900">${commitment.user.name}</span>
                                <span class="text-xs text-gray-400">Just now</span>
                            </div>
                            <p class="goal-text text-gray-700">🎯 ${commitment.goal}</p>
                        </div>
                        ${actionButtonHTML}
                    </div>
                </div>
            `;

            commitmentsList.insertAdjacentHTML('afterbegin', html);
        }

        // ⚡ NEW: Visually cross out a completed goal and hide the button
        function markCommitmentAsDone(id) {
            const item = document.querySelector(`.commitment-item[data-id="${id}"]`);
            if (item) {
                const text = item.querySelector('.goal-text');
                if (text) {
                    text.classList.remove('text-gray-700');
                    text.classList.add('line-through', 'text-gray-500');
                }
                item.classList.remove('bg-white', 'border-gray-300');
                item.classList.add('bg-gray-50', 'border-gray-200');
                
                const form = item.querySelector('.toggle-commitment-form');
                if (form) {
                    // Replace form with a completed badge
                    form.outerHTML = '<span class="text-xs font-bold text-green-600 bg-green-100 px-2 py-1 rounded">Completed</span>';
                }
            }
        }

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

                const html = `
                    <li class="text-sm border-l-2 pl-3 py-1 ${colorClass} rounded-r animate-fade-in">
                        <span class="block font-medium">${logData.message}</span>
                        <span class="text-xs opacity-75">Just now</span>
                    </li>
                `;
                
                activityLog.insertAdjacentHTML('afterbegin', html);
            });

        // 3. Hijack the chat form so we send messages via AJAX (no page reloads!)
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

        // Helper function to build the Chat Bubble HTML
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