export function initResources(currentUserId, roomChannel) {
    const resourceForm = document.getElementById('resource-form');
    const resourcesList = document.getElementById('resources-list');
    const resourceTitle = document.getElementById('resource-title');
    const resourceUrl = document.getElementById('resource-url');

    if (!resourceForm) return;

    roomChannel.listen('ResourceAdded', (event) => {
        if (event.resource.user_id !== currentUserId) {
            appendResource(event.resource);
        }
    });

    resourceForm.addEventListener('submit', function (e) {
        e.preventDefault();
        const title = resourceTitle.value;
        const url = resourceUrl.value;
        
        if (!title.trim() || !url.trim()) return;

        resourceTitle.value = '';
        resourceUrl.value = '';

        axios.post(resourceForm.action, { title: title, url: url })
             .then(response => appendResource(response.data.resource))
             .catch(error => console.error(error));
    });

    function appendResource(resource) {
        const empty = resourcesList.querySelector('.empty-resources-text');
        if (empty) empty.remove();

        const html = `
            <div class="p-3 bg-gray-50 border border-gray-100 rounded-lg shadow-sm hover:shadow-md transition animate-fade-in">
                <a href="${resource.url}" target="_blank" rel="noopener noreferrer" class="font-bold text-indigo-600 hover:text-indigo-800 hover:underline text-sm block truncate">
                    🔗 ${resource.title}
                </a>
                <div class="text-xs text-gray-500 mt-2 flex justify-between items-center">
                    <span>By ${resource.user.name}</span>
                    <span>Just now</span>
                </div>
            </div>
        `;
        resourcesList.insertAdjacentHTML('afterbegin', html);
    }
}