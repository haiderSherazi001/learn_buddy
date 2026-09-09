export function initHeader(roomChannel) {
    roomChannel.listen("RoomHeaderUpdated", (event) => {
        const wrapper = document.getElementById("room-header-wrapper");
        if (wrapper) {
            wrapper.outerHTML = event.html;

            const csrfToken = document.querySelector(
                'meta[name="csrf-token"]',
            ).content;
            document
                .querySelectorAll('input[name="_token"]')
                .forEach((input) => {
                    input.value = csrfToken;
                });
        }
    });
}
