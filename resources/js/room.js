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
                    form.outerHTML = '<span class="text-xs font-bold text-green-600 bg-green-100 px-2 py-1 rounded">Completed</span>';
                }

                commitmentsList.appendChild(item);
            }
        }

        // --- STANDUPS LOGIC ---
        const standupForm = document.getElementById('standup-form');
        const standupsList = document.getElementById('standups-list');

        if (standupForm) {
            
            // 1. Listen for new standups and replies via WebSockets
            Echo.private(`room.${roomId}`)
                .listen('StandupCreated', (event) => {
                    if (event.standup.user_id !== currentUserId) {
                        appendStandup(event.standup);
                    }
                })
                .listen('StandupReplyCreated', (event) => {
                    if (event.reply.user_id !== currentUserId) {
                        appendReply(event.reply.standup_id, event.reply);
                    }
                });

            // 2. Submit new Standup
            standupForm.addEventListener('submit', function(e) {
                e.preventDefault();
                const didInput = document.getElementById('standup-did');
                const blockersInput = document.getElementById('standup-blockers');
                
                const data = {
                    what_i_did: didInput.value,
                    blockers: blockersInput.value
                };

                didInput.value = '';
                blockersInput.value = '';

                axios.post(standupForm.action, data)
                     .then(response => {
                         appendStandup(response.data.standup);
                     })
                     .catch(error => console.error(error));
            });

            // 3. Event Delegation for Replies (since standups can be added dynamically)
            standupsList.addEventListener('submit', function(e) {
                if (e.target.matches('form.reply-form')) {
                    e.preventDefault();
                    const form = e.target;
                    const standupId = form.dataset.id;
                    const input = form.querySelector('.reply-input');
                    const text = input.value;
                    
                    if (!text.trim()) return;
                    input.value = '';

                    axios.post(form.action, { body: text })
                         .then(response => {
                             appendReply(standupId, response.data.comment);
                         })
                         .catch(error => console.error(error));
                }
            });
        }

        // --- Helper Functions ---
        
        function appendStandup(standup) {
            const empty = standupsList.querySelector('.empty-text');
            if (empty) empty.remove();

            const blockerHtml = standup.blockers 
                ? `<p class="text-red-600 mt-2 text-sm bg-red-50 p-2 rounded border border-red-100"><strong>Blocker:</strong> ${standup.blockers}</p>` 
                : '';

            const html = `
                <div class="standup-item p-4 bg-white border rounded-lg shadow-sm animate-fade-in" data-id="${standup.id}">
                    <div class="flex items-center space-x-2 mb-2">
                        <span class="font-bold text-gray-900">${standup.user.name}</span>
                        <span class="text-xs text-gray-400">Just now</span>
                    </div>
                    <p class="text-gray-700"><strong>Progress:</strong> ${standup.what_i_did}</p>
                    ${blockerHtml}

                    <div class="mt-4 pt-4 border-t border-gray-100 pl-4 border-l-2 border-indigo-100">
                        <div class="comments-list"></div> <!-- ⚡ Cleaned up! -->
                        
                        <form action="/standups/${standup.id}/comments" method="POST" class="reply-form mt-3 flex gap-2" data-id="${standup.id}">
                            <input type="hidden" name="_token" value="${document.querySelector('meta[name="csrf-token"]').content}">
                            <input type="text" name="body" placeholder="Reply or help unblock..." required autocomplete="off" class="reply-input text-sm flex-1 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 py-1 px-3">
                            <button type="submit" class="text-sm px-3 py-1 bg-indigo-50 text-indigo-700 rounded-md font-bold border border-indigo-200 hover:bg-indigo-100 transition">Reply</button>
                        </form>
                    </div>
                </div>
            `;
            standupsList.insertAdjacentHTML('afterbegin', html);
        }

        function appendReply(standupId, reply) {
            const standupCard = document.querySelector(`.standup-item[data-id="${standupId}"]`);
            if (standupCard) {
                const commentsList = standupCard.querySelector('.comments-list');
                const html = `
                    <div class="mb-3 animate-fade-in">
                        <p class="text-xs text-gray-500 font-bold mb-1">
                            ${reply.user.name} 
                            <span class="font-normal text-gray-400">&bull; Just now</span>
                        </p>
                        <p class="text-sm text-gray-700 bg-gray-50 p-2 rounded-lg inline-block">${reply.body}</p>
                    </div>
                `;
                commentsList.insertAdjacentHTML('beforeend', html);
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