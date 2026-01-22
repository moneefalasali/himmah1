<?php $__env->startSection('content'); ?>
<div class="container mx-auto p-4 h-screen flex flex-col" x-data="chatApp()">
    <div class="bg-white shadow-lg rounded-lg flex-1 flex flex-col overflow-hidden">
            <style>
            /* Chat bubble styles (WhatsApp/Telegram-like) */
            .chat-row { display: flex; margin-bottom: 12px; }
            .chat-row.align-end { justify-content: flex-end; }
            .chat-row.align-start { justify-content: flex-start; }
            .chat-avatar { width:36px; height:36px; border-radius:50%; overflow:hidden; margin:0 8px; flex:0 0 36px; }
            .chat-bubble { max-width: 78%; padding:10px 12px; border-radius:16px; box-shadow:0 1px 0 rgba(0,0,0,0.05); display:block; }
            .chat-bubble--me { background:#dcf8c6; color:#000; border-bottom-right-radius:4px; }
            .chat-bubble--other { background:#2b6cb0; color:#fff; border-bottom-left-radius:4px; }
            .chat-meta { font-size:11px; color:rgba(255,255,255,0.85); margin-top:6px; }
            .chat-author { font-weight:600; font-size:12px; color:inherit; margin-bottom:4px; }
            .chat-attachment { margin-top:8px; }
            .chat-attachment img { max-width:200px; border-radius:8px; }
            .bubble-icon { font-size:14px; vertical-align:middle; margin-right:8px; }
            .chat-bubble--me .chat-meta { color:#444; }
            .chat-bubble--other a { color:#e6f2ff; }
        </style>
        <!-- Header -->
        <div class="p-4 border-b bg-blue-600 text-white flex justify-between items-center">
            <div class="flex items-center">
                <?php if($room->type === 'course' && $room->course): ?>
                    <img src="<?php echo e($room->course->thumbnail_url); ?>" class="w-10 h-10 rounded-full mr-3 border-2 border-white">
                <?php else: ?>
                    <div class="w-10 h-10 rounded-full bg-white/20 flex items-center justify-center mr-3 border-2 border-white">
                        <i class="fas fa-headset"></i>
                    </div>
                <?php endif; ?>
                <div>
                    <h2 class="font-bold"><?php echo e($room->name); ?></h2>
                    <p class="text-xs opacity-75">
                        <?php if($room->type === 'service'): ?>
                            دردشة خاصة مع الإدارة (الدعم الفني)
                        <?php else: ?>
                            دردشة جماعية (الطلاب، المعلم، والإدارة)
                        <?php endif; ?>
                    </p>
                </div>
            </div>
            <div class="text-sm">
                <span x-show="online" class="bg-green-400 w-2 h-2 rounded-full inline-block mr-1"></span>
                <span x-text="online ? 'متصل' : 'جاري الاتصال...'"></span>
            </div>
        </div>

        <!-- Messages Area -->
        <div class="flex-1 overflow-y-auto p-4 space-y-4 bg-gray-50" id="messages-container">
            <template x-for="msg in messages" :key="msg.id">
                <div :class="msg.user_id == <?php echo e(auth()->id()); ?> ? 'chat-row align-end' : 'chat-row align-start'">
                    <!-- show avatar on left for others, on right for me -->
                    <template x-if="msg.user_id != <?php echo e(auth()->id()); ?>">
                        <div class="chat-avatar">
                            <img :src="msg.user.avatar_url || '<?php echo e(asset('assets/images/default-avatar.png')); ?>'" alt="avatar" style="width:100%;height:100%;object-fit:cover;">
                        </div>
                    </template>

                    <div :class="msg.user_id == <?php echo e(auth()->id()); ?> ? 'chat-bubble chat-bubble--me' : 'chat-bubble chat-bubble--other'">
                        <div class="chat-author" x-text="msg.user.name"></div>
                        <div class="flex items-start">
                            <template x-if="msg.user_id == <?php echo e(auth()->id()); ?>">
                                <span class="bubble-icon text-gray-700"><i class="fas fa-check"></i></span>
                            </template>
                            <template x-if="msg.user_id != <?php echo e(auth()->id()); ?>">
                                <span class="bubble-icon text-white"><i class="fas fa-user"></i></span>
                            </template>
                            <div class="message-content" x-text="msg.content || msg.message"></div>
                        </div>

                        <template x-if="msg.attachments && msg.attachments.length">
                            <div class="chat-attachment">
                                <template x-for="att in msg.attachments" :key="att.id">
                                    <div class="mb-2">
                                        <template x-if="att.file_type && att.file_type.startsWith('image')">
                                            <img :src="att.url || att.file_path" alt="image attachment">
                                        </template>
                                        <template x-if="att.file_type && att.file_type.startsWith('audio')">
                                            <audio controls :src="att.url || att.file_path"></audio>
                                        </template>
                                        <template x-if="att.file_type && !att.file_type.startsWith('image') && !att.file_type.startsWith('audio')">
                                            <a :href="att.url || att.file_path" target="_blank" class="text-sm text-blue-600 underline">فتح الملف</a>
                                        </template>
                                    </div>
                                </template>
                            </div>
                        </template>

                        <div class="chat-meta text-end" x-text="formatTime(msg.created_at)"></div>
                        <div class="chat-controls text-xs mt-2 flex justify-end gap-2">
                            <template x-if="msg.user_id == <?php echo e(auth()->id()); ?>">
                                <button @click.prevent="deleteMessage(msg, 'everyone')" class="text-red-600 hover:underline">حذف للجميع</button>
                            </template>
                            <button @click.prevent="deleteMessage(msg, 'me')" class="text-gray-600 hover:underline">حذف لي</button>
                        </div>
                    </div>

                    <template x-if="msg.user_id == <?php echo e(auth()->id()); ?>">
                        <div class="chat-avatar">
                            <img :src="msg.user.avatar_url || '<?php echo e(asset('assets/images/default-avatar.png')); ?>'" alt="avatar" style="width:100%;height:100%;object-fit:cover;">
                        </div>
                    </template>
                </div>
            </template>
        </div>

        <!-- Input Area -->
        <div class="p-4 border-t bg-white">
            <form @submit.prevent="sendMessage" onsubmit="event.preventDefault();" action="javascript:void(0);" class="flex items-center gap-2" enctype="multipart/form-data">
                    <input type="file" id="chatFiles" multiple accept="image/*,audio/*,video/*,.pdf,.doc,.docx" class="hidden" @change="onFileChange($event)">

                    <button type="button" @click.prevent="document.getElementById('chatFiles').click()" class="px-3 py-2 bg-gray-100 hover:bg-gray-200 rounded-lg text-gray-700">
                        <i class="fas fa-paperclip"></i>
                    </button>

                    <button type="button" @click.prevent="toggleRecording" class="px-3 py-2 ml-2 rounded-lg text-white" :class="recording ? 'bg-red-500 hover:bg-red-600' : 'bg-gray-800 hover:bg-gray-900'" title="تسجيل صوت">
                        <i class="fas" :class="recording ? 'fa-stop' : 'fa-microphone'"></i>
                    </button>

                          <input type="text" x-model="newMessage" placeholder="اكتب رسالتك هنا..." 
                              class="flex-1 border rounded-full px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">

                        <div class="text-sm text-gray-500 mr-2" x-text="pendingFiles.length ? (pendingFiles.length + ' ملف/ملفات مرفقة') : 'لم يتم اختيار ملف'"></div>

                        <button type="submit" @click.prevent="sendMessage" :disabled="sending" class="bg-blue-600 text-white p-3 rounded-full hover:bg-blue-700 disabled:opacity-50">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M5 13l4 4L19 7"></path></svg>
                    </button>
                </form>
        </div>
    </div>
</div>

<script>
function chatApp() {
    return {
        // pass messages as a plain array so Alpine can manipulate them safely
        messages: <?php echo json_encode($messages->items(), 15, 512) ?>,
        newMessage: '',
        sending: false,
        online: false,
        // client-side queued files (from file input or recorded audio)
        pendingFiles: [],
        recording: false,
        mediaRecorder: null,
        recordedChunks: [],
        
        init() {
            this.scrollToBottom();
            this.listenForMessages();
            this.startPollingIfNeeded();
        },

        listenForMessages() {
            // منطق الـ Real-time باستخدام Laravel Echo
            if (typeof Echo !== 'undefined') {
                Echo.private('chat.' + <?php echo e($room->id); ?>)
                    .listen('.message.sent', (e) => {
                        this.messages.push(e.message);
                        this.scrollToBottom();
                    })
                    .listen('.message.deleted', (e) => {
                        // remove deleted message locally
                        const id = e.messageId || e.message_id || null;
                        if (id) this.messages = this.messages.filter(m => m.id !== id);
                    })
                    .on('connected', () => this.online = true)
                    .on('disconnected', () => this.online = false);
            }
        },

        startPollingIfNeeded() {
            // Always start a polling fallback to ensure persisted messages are received
            // Polling is cheap and de-duplicated client-side by message id.
            if (!this._pollInterval) this._pollInterval = setInterval(() => this.fetchNewMessages(), 5000);
        },

        async fetchNewMessages() {
            try {
                const knownMaxId = this.messages.length ? Math.max(...this.messages.map(m => m.id)) : 0;
                const resp = await axios.get('<?php echo e(route('chat.messages.json', $room)); ?>');
                const data = resp && resp.data ? resp.data : [];
                // append only messages with id greater than knownMaxId
                const toAppend = data.filter(m => m.id > knownMaxId).sort((a,b) => a.id - b.id);
                if (toAppend.length) {
                    for (const m of toAppend) this.messages.push(m);
                    this.scrollToBottom();
                }
            } catch (err) {
                // silent; polling can fail when unauthenticated
                console.debug('polling messages failed', err);
            }
        },

        async deleteMessage(msg, action = 'me') {
            try {
                await axios.post('<?php echo e(route('chat.messages.delete', ['message' => '__MSG__'])); ?>'.replace('__MSG__', msg.id), { action });
                // remove locally for current user
                if (action === 'me' || (action === 'everyone' && msg.user_id == <?php echo e(auth()->id()); ?>)) {
                    this.messages = this.messages.filter(m => m.id !== msg.id);
                }
            } catch (err) {
                console.error('delete failed', err);
                alert('فشل حذف الرسالة');
            }
        },

        async sendMessage() {
            // require content or at least one file
            const fileInput = document.getElementById('chatFiles');
            const fileList = fileInput ? fileInput.files : [];
            if (!this.newMessage && (fileList.length === 0 && this.pendingFiles.length === 0)) return;

            this.sending = true;
            try {
                const form = new FormData();

                // If no textual message but files/recordings exist, create a content summary
                let contentToSend = this.newMessage || '';
                if (!contentToSend && (fileList.length > 0 || this.pendingFiles.length > 0)) {
                    const names = [];
                    for (let i = 0; i < fileList.length; i++) names.push(fileList[i].name);
                    for (let i = 0; i < this.pendingFiles.length; i++) names.push(this.pendingFiles[i].name);
                    contentToSend = names.join(', ');
                }

                form.append('content', contentToSend);

                // append selected files
                for (let i = 0; i < fileList.length; i++) {
                    form.append('files[]', fileList[i]);
                }

                // append recorded files
                for (let i = 0; i < this.pendingFiles.length; i++) {
                    form.append('files[]', this.pendingFiles[i]);
                }

                const response = await axios.post('<?php echo e(route('chat.messages.store', $room)); ?>', form, {
                    headers: { 'Content-Type': 'multipart/form-data' }
                });

                // Validate response shape before pushing
                const data = response && response.data ? response.data : null;
                if (!data) {
                    console.error('Empty response from server', response);
                    alert('خطأ في الخادم: استجابة غير متوقعة');
                    return;
                }

                // If the API returned the message directly (Eloquent JSON), push it
                if (data.id) {
                    this.messages.push(data);
                }
                // Or if wrapped in { message: {...} }
                else if (data.message && data.message.id) {
                    this.messages.push(data.message);
                } else {
                    console.error('Unexpected message response', data);
                    alert('استجابة غير متوقعة من الخادم؛ راجع الكونسول');
                }
                this.newMessage = '';
                if (fileInput) fileInput.value = null;
                this.pendingFiles = [];
                this.recordedChunks = [];
                this.scrollToBottom();
            } catch (error) {
                alert('فشل إرسال الرسالة');
                console.error(error);
            } finally {
                this.sending = false;
            }
        },

        onFileChange(e) {
            const files = e.target.files || [];
            for (let i = 0; i < files.length; i++) {
                this.pendingFiles.push(files[i]);
            }
            // clear the input so same file can be re-selected later if needed
            e.target.value = null;
        },

        toggleRecording() {
            if (this.recording) {
                this.stopRecording();
            } else {
                this.startRecording();
            }
        },
        async startRecording() {
            if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
                alert('التسجيل الصوتي غير مدعوم في متصفحك');
                return;
            }
            try {
                const stream = await navigator.mediaDevices.getUserMedia({ audio: true });
                this.recordedChunks = [];
                this.mediaRecorder = new MediaRecorder(stream);
                this.mediaRecorder.ondataavailable = (e) => { if (e.data && e.data.size) this.recordedChunks.push(e.data); };
                this.mediaRecorder.onstop = () => {
                    const blob = new Blob(this.recordedChunks, { type: 'audio/webm' });
                    const file = new File([blob], `voice-${Date.now()}.webm`, { type: 'audio/webm' });
                    this.pendingFiles.push(file);
                };
                this.mediaRecorder.start();
                this.recording = true;
            } catch (err) {
                console.error(err);
                alert('فشل بدء التسجيل');
            }
        },

        stopRecording() {
            if (this.mediaRecorder && this.recording) {
                this.mediaRecorder.stop();
            }
            this.recording = false;
        },

        formatTime(dateStr) {
            const date = new Date(dateStr);
            return date.toLocaleTimeString('ar-SA', { hour: '2-digit', minute: '2-digit' });
        },

        scrollToBottom() {
            setTimeout(() => {
                const container = document.getElementById('messages-container');
                if (container) {
                    container.scrollTop = container.scrollHeight;
                }
            }, 100);
        }
    }
}
</script>
<script>
(function(){
    // If Alpine is running, prefer Alpine component. Otherwise provide a native JS fallback
    if (window.Alpine) return;

    const form = document.querySelector('form[action="javascript:void(0);"]') || document.querySelector('form');
    if (!form) return;

    const sendBtn = form.querySelector('button[type="submit"]');
    const fileInput = document.getElementById('chatFiles');
    const messageInput = form.querySelector('input[type="text"]');
    const messagesContainer = document.getElementById('messages-container');

        function nativeSend(e){
        e.preventDefault();
        const url = '<?php echo e(route('chat.messages.store', $room)); ?>';
        const tokenMeta = document.querySelector('meta[name="csrf-token"]');
        const csrf = tokenMeta ? tokenMeta.getAttribute('content') : '';

        const fd = new FormData();
        let content = messageInput ? messageInput.value : '';
        if (!content && fileInput && fileInput.files && fileInput.files.length) {
            const names = [];
            for (let i = 0; i < fileInput.files.length; i++) names.push(fileInput.files[i].name);
            content = names.join(', ');
        }
        fd.append('content', content);

        if (fileInput && fileInput.files.length) {
            for (let i = 0; i < fileInput.files.length; i++) fd.append('files[]', fileInput.files[i]);
        }

        fetch(url, {
            method: 'POST',
            credentials: 'same-origin',
            headers: {
                'X-CSRF-TOKEN': csrf
            },
            body: fd
        })
        .then(async (res) => {
            let data = null;
            try { data = await res.json(); } catch (err) { data = null; }
            if (res.ok && data) {
                const msg = data.id ? data : (data.message || null);
                    if (msg && messagesContainer) {
                    const wrapper = document.createElement('div');
                    wrapper.className = (msg.user_id == <?php echo e(auth()->id()); ?>) ? 'chat-row align-end' : 'chat-row align-start';
                    const bubbleClass = (msg.user_id == <?php echo e(auth()->id()); ?>) ? 'chat-bubble chat-bubble--me' : 'chat-bubble chat-bubble--other';
                    const text = msg.content || msg.message || '';
                    const iconHtml = (msg.user_id == <?php echo e(auth()->id()); ?>) ? '<span class="bubble-icon text-gray-700"><i class="fas fa-check"></i></span>' : '<span class="bubble-icon text-white"><i class="fas fa-user"></i></span>';
                    wrapper.innerHTML = `<div class="${bubbleClass}"><div class="chat-author">${msg.user?.name || ''}</div><div class="flex items-start">${iconHtml}<div class="message-content">${text}</div></div><div class="chat-meta text-end">${new Date(msg.created_at).toLocaleTimeString('ar-SA',{hour:'2-digit',minute:'2-digit'})}</div></div>`;
                    messagesContainer.appendChild(wrapper);
                    messagesContainer.scrollTop = messagesContainer.scrollHeight;
                }
                if (messageInput) messageInput.value = '';
                if (fileInput) fileInput.value = null;
            } else {
                alert('فشل إرسال الرسالة — استجابة الخادم غير صحيحة');
                console.error('send error', res, data);
            }
        })
        .catch((err) => { alert('فشل إرسال الرسالة'); console.error(err); });
    }

    if (sendBtn) sendBtn.addEventListener('click', nativeSend);
    form.addEventListener('submit', nativeSend);
})();
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\himm23\resources\views/chat/show.blade.php ENDPATH**/ ?>