export function initStandups(currentUserId, roomChannel) {
    const standupForm = document.getElementById('standup-form');
    const standupsList = document.getElementById('standups-list');

    if (!standupForm) return;

    // 1. Listen for WebSockets
    roomChannel
        .listen('StandupCreated', (event) => {
            if (event.standup.user_id !== currentUserId) appendStandup(event.standup);
        })
        .listen('StandupReplyCreated', (event) => {
            if (event.reply.user_id !== currentUserId) appendReply(event.reply.standup_id, event.reply);
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
             .then(response => appendStandup(response.data.standup))
             .catch(error => console.error(error));
    });

    // 3. Submit Reply (Event Delegation)
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
                 .then(response => appendReply(standupId, response.data.comment))
                 .catch(error => console.error(error));
        }
    });

    // Helper Functions
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
                    <div class="comments-list"></div>
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
                        ${reply.user.name} <span class="font-normal text-gray-400">&bull; Just now</span>
                    </p>
                    <p class="text-sm text-gray-700 bg-gray-50 p-2 rounded-lg inline-block">${reply.body}</p>
                </div>
            `;
            commentsList.insertAdjacentHTML('beforeend', html);
        }
    }
}