export function initCommitments(currentUserId, roomChannel) {
    const commitmentForm = document.getElementById('commitment-form');
    const commitmentInput = document.getElementById('commitment-input');
    const commitmentsList = document.getElementById('commitments-list');

    if (!commitmentForm) return;

    // 1. Listen for WebSockets
    roomChannel
        .listen('CommitmentCreated', (event) => {
            if (event.commitment.user_id !== currentUserId) {
                appendCommitment(event.commitment);
            }
        })
        .listen('CommitmentUpdated', (event) => {
            if (event.commitment.is_completed) {
                markCommitmentAsDone(event.commitment.id);
            }
        });

    // 2. Submit new commitment
    commitmentForm.addEventListener('submit', function (e) {
        e.preventDefault();
        const goalText = commitmentInput.value;
        if (!goalText.trim()) return;

        commitmentInput.value = ''; 

        axios.post(commitmentForm.action, { goal: goalText })
             .then(response => appendCommitment(response.data.commitment))
             .catch(error => console.error(error));
    });

    // 3. Mark done (Event Delegation)
    commitmentsList.addEventListener('submit', function (e) {
        if (e.target.matches('form.toggle-commitment-form')) {
            e.preventDefault();
            const form = e.target;
            const commitmentId = form.dataset.id;

            markCommitmentAsDone(commitmentId);

            axios.post(form.action, { _method: 'PATCH' })
                 .catch(error => console.error(error));
        }
    });

    // Helper functions
    function appendCommitment(commitment) {
        const emptyText = commitmentsList.querySelector('p.italic');
        if (emptyText) emptyText.remove();

        const isMe = commitment.user_id === currentUserId;
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
            if (form) form.outerHTML = '<span class="text-xs font-bold text-green-600 bg-green-100 px-2 py-1 rounded">Completed</span>';

            commitmentsList.appendChild(item);
        }
    }
}