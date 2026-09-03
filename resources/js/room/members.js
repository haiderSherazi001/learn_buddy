export function initMembers(roomChannel) {
    roomChannel.listen('CohortMembersUpdated', (event) => {
        const wrapper = document.getElementById('cohort-members-wrapper');
        
        if (wrapper) {
            wrapper.outerHTML = event.html;
        }
    });
}