export function initChatAndActivity(currentUserId, roomChannel, roomId) {
    const chatContainer = document.getElementById("chat-messages");
    const chatForm = document.getElementById("chat-form");
    const chatInput = document.getElementById("chat-input");
    const activityLog = document.getElementById("activity-log");

    // Media & UI elements
    const mediaUpload = document.getElementById("media-upload");
    const attachBtn = document.getElementById("attach-btn");
    const previewContainer = document.getElementById("media-preview-container");
    const previewFilename = document.getElementById("preview-filename");
    const previewIcon = document.getElementById("preview-icon");
    const cancelMediaBtn = document.getElementById("cancel-media-btn");

    // Recording elements
    const recordBtn = document.getElementById("record-btn");
    const micIcon = document.getElementById("mic-icon");
    const trashIcon = document.getElementById("trash-icon");
    const recordingUi = document.getElementById("recording-ui");
    const recordTime = document.getElementById("record-time");

    let pendingFile = null;
    let mediaRecorder = null;
    let audioChunks = [];
    let recordInterval = null;
    let seconds = 0;
    let isDiscarding = false;

    if (!chatContainer || !chatForm) return;
    chatContainer.scrollTop = chatContainer.scrollHeight;

    // --- WEBSOCKETS ---
    roomChannel.listen("MessageSent", (event) => {
        if (event.user.id !== currentUserId) {
            appendMessage(event.message, event.user);

            const chatTabContent = document.getElementById("tab-chat");
            if (chatTabContent && chatTabContent.classList.contains("hidden")) {
                const chatBadge = document.getElementById("chat-badge");
                if (chatBadge) chatBadge.classList.remove("hidden");
            }
        }
    });

    roomChannel.listen("RoomEventBroadcast", (e) => {
        if (!activityLog) return;
        const logData = e.event;

        const currentUserName = document
            .getElementById("room-data")
            .dataset.userName.trim();
        let messageText = logData.message.trim();
        let isMe = false;

        // ⚡ CHANGE: Check if the message CONTAINS your name anywhere, ignoring upper/lowercase
        if (messageText.toLowerCase().includes(currentUserName.toLowerCase())) {
            isMe = true;

            // Regex to find your name anywhere in the string and replace it with "You"
            const nameRegex = new RegExp(currentUserName, "i");
            messageText = messageText.replace(nameRegex, "You");

            // Replace "their" with "your"
            messageText = messageText.replace(/\btheir\b/gi, "your");

            // (Optional grammar fix in case it says "sherazi has" -> "You have")
            messageText = messageText.replace(/\bhas\b/gi, "have");
        }

        const colorClass =
            logData.type === "leave"
                ? "border-red-400 text-red-700 bg-red-50"
                : "border-blue-400 text-blue-700 bg-blue-50";
        const html = `<li class="text-sm border-l-2 pl-3 py-1 ${colorClass} rounded-r animate-fade-in"><span class="block font-medium">${messageText}</span><span class="text-xs opacity-75">Just now</span></li>`;
        activityLog.insertAdjacentHTML("afterbegin", html);

        // ONLY SHOW THE DOT IF SOMEONE ELSE DID IT!
        if (!isMe) {
            const activityTabContent = document.getElementById("tab-activity");
            if (
                activityTabContent &&
                activityTabContent.classList.contains("hidden")
            ) {
                const activityBadge = document.getElementById("activity-badge");
                if (activityBadge) activityBadge.classList.remove("hidden");
            }
        }
    });

    // --- 1. FILE SELECTION ---
    if (attachBtn && mediaUpload) {
        attachBtn.addEventListener("click", () => mediaUpload.click());

        mediaUpload.addEventListener("change", function (e) {
            pendingFile = e.target.files[0];
            if (!pendingFile) return;

            previewFilename.textContent = pendingFile.name;
            const isImage = pendingFile.type.startsWith("image/");
            previewIcon.innerHTML = isImage
                ? `<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>`
                : `<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>`;

            previewContainer.classList.remove("hidden");
            chatInput.focus();
        });

        cancelMediaBtn.addEventListener("click", clearPreview);
    }

    function clearPreview() {
        pendingFile = null;
        mediaUpload.value = "";
        previewContainer.classList.add("hidden");
    }

    // --- 2. VOICE RECORDING ---
    if (recordBtn) {
        recordBtn.addEventListener("click", async () => {
            if (mediaRecorder && mediaRecorder.state === "recording") {
                isDiscarding = true;
                mediaRecorder.stop();
                return;
            }

            try {
                const stream = await navigator.mediaDevices.getUserMedia({
                    audio: true,
                });
                mediaRecorder = new MediaRecorder(stream);
                audioChunks = [];

                mediaRecorder.ondataavailable = (e) => {
                    if (e.data.size > 0) audioChunks.push(e.data);
                };

                mediaRecorder.onstop = async () => {
                    stream.getTracks().forEach((track) => track.stop());
                    clearInterval(recordInterval);

                    if (!isDiscarding) {
                        const audioBlob = new Blob(audioChunks, {
                            type: "audio/webm",
                        });
                        const formData = new FormData();
                        formData.append("file", audioBlob, "voice.webm");
                        formData.append("type", "audio");
                        await uploadMedia(formData);
                    }

                    micIcon.classList.remove("hidden");
                    trashIcon.classList.add("hidden");
                    recordingUi.classList.add("hidden");
                    chatInput.disabled = false;
                    chatInput.focus();
                };

                mediaRecorder.start();
                isDiscarding = false;

                micIcon.classList.add("hidden");
                trashIcon.classList.remove("hidden");
                recordingUi.classList.remove("hidden");
                chatInput.disabled = true;

                seconds = 0;
                recordTime.textContent = "0:00";
                recordInterval = setInterval(() => {
                    seconds++;
                    const m = Math.floor(seconds / 60);
                    const s = seconds % 60;
                    recordTime.textContent = `${m}:${s.toString().padStart(2, "0")}`;
                }, 1000);
            } catch (err) {
                alert("Microphone access denied.");
            }
        });
    }

    // --- 3. SUBMIT / SEND BUTTON ---
    chatForm.addEventListener("submit", async function (e) {
        e.preventDefault();

        if (mediaRecorder && mediaRecorder.state === "recording") {
            isDiscarding = false;
            mediaRecorder.stop();
            return;
        }

        const text = chatInput.value.trim();

        if (pendingFile) {
            const formData = new FormData();
            formData.append("file", pendingFile);
            const type = pendingFile.type.startsWith("image/")
                ? "image"
                : "document";
            formData.append("type", type);
            await uploadMedia(formData);
            clearPreview();
        }

        if (text) {
            chatInput.value = "";
            axios
                .post(chatForm.action, { body: text, type: "text" })
                .then((response) =>
                    appendMessage(response.data.message, response.data.user),
                )
                .catch((error) => console.error(error));
        }
    });

    async function uploadMedia(formData) {
        try {
            const response = await axios.post(
                `/rooms/${roomId}/media`,
                formData,
                {
                    headers: { "Content-Type": "multipart/form-data" },
                },
            );
            appendMessage(response.data.message, response.data.user);
        } catch (error) {
            console.error(error);
            alert("Failed to upload media.");
        }
    }

    // --- RENDER MESSAGE ---
    function appendMessage(message, user) {
        const isMe = user.id === currentUserId;
        const alignClass = isMe ? "items-end" : "items-start";
        const bubbleClass = isMe
            ? "bg-indigo-600 text-white rounded-tr-sm"
            : "bg-gray-100 text-gray-800 rounded-tl-sm";

        let innerContent = "";
        if (message.type === "image") {
            innerContent = `<img src="/storage/${message.body}" class="rounded-lg w-full h-auto max-h-64 object-contain cursor-pointer border border-black/10" onclick="window.open(this.src, '_blank')">`;
        } else if (message.type === "audio") {
            innerContent = `
                <div class="flex items-center gap-2 py-1">
                    <audio controls controlsList="nodownload noplaybackrate" src="/storage/${message.body}" class="h-10 w-48 rounded-full shadow-sm"></audio>
                </div>`;
        } else if (message.type === "document") {
            innerContent = `
                <a href="/storage/${message.body}" target="_blank" class="flex items-center gap-3 p-2 bg-black/5 rounded-lg hover:bg-black/10 transition">
                    <div class="p-2 bg-indigo-100 text-indigo-600 rounded shrink-0">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                    </div>
                    <span class="font-bold underline truncate">View Document</span>
                </a>`;
        } else {
            innerContent = message.body;
        }

        const html = `
            <div class="flex flex-col ${alignClass}">
                <span class="text-[10px] text-gray-400 mb-0.5 mx-1">${user.name}</span>
                <div class="px-3 py-2 text-sm shadow-sm max-w-[85%] break-words rounded-2xl ${bubbleClass} overflow-hidden">
                    ${innerContent}
                </div>
            </div>
        `;
        chatContainer.insertAdjacentHTML("beforeend", html);
        chatContainer.scrollTop = chatContainer.scrollHeight;
    }
}
