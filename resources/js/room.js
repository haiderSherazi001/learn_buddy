// resources/js/room.js

import './bootstrap'; 
import { initCommitments } from './room/commitments';
import { initStandups } from './room/standups';
import { initResources } from './room/resources';
import { initChatAndActivity } from './room/chat';
import { initMembers } from './room/members';

document.addEventListener('DOMContentLoaded', () => {
    const roomData = document.getElementById('room-data');
    if (!roomData) return;

    const roomId = roomData.dataset.roomId;
    const currentUserId = parseInt(roomData.dataset.userId);

    // ⚡ We create the WebSocket connection ONCE here...
    const roomChannel = Echo.private(`room.${roomId}`);

    // ...and pass it to our neat little feature modules!
    initCommitments(currentUserId, roomChannel);
    initStandups(currentUserId, roomChannel);
    initResources(currentUserId, roomChannel);
    initChatAndActivity(currentUserId, roomChannel);
    initMembers(roomChannel);
});