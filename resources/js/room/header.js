export function initHeader(roomChannel) {
    roomChannel.listen('RoomHeaderUpdated', (event) => {
        const wrapper = document.getElementById('room-header-wrapper');
        if (wrapper) {
            wrapper.outerHTML = event.html; // Instantly swap the header!
        }
    });
}