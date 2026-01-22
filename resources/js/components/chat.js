import axios from 'axios';

export default function initChat(roomId, user) {
    if (!window.Echo) return;

    const channel = window.Echo.private('chat.' + roomId);

    channel.listen('.message.sent', (e) => {
        const msg = e.message;
        // append to chat UI
        const container = document.querySelector('#chat-messages-' + roomId);
        if (!container) return;

        const el = document.createElement('div');
        el.className = 'chat-message';
        el.innerHTML = `<strong>${msg.user.name}</strong>: ${msg.body}`;
        container.appendChild(el);
        container.scrollTop = container.scrollHeight;
    });

    // send message helper
    window.sendChatMessage = function(roomId, body, attachments = []) {
        return axios.post('/chat/' + roomId + '/message', { body, attachments });
    }
}
