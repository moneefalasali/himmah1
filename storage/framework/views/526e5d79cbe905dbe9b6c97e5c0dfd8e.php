

<?php $__env->startSection('title', 'مساعد الكورس — ' . $course->title); ?>

<?php $__env->startSection('student_content'); ?>
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>مساعد همّه الذكي: <?php echo e($course->title); ?></h2>
        <div>
            <a href="<?php echo e(route('courses.show', $course)); ?>" class="btn btn-outline-secondary">العودة للكورس</a>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card mb-3">
                <div class="card-body">
                    <div id="chatBox" style="height:420px; overflow:auto; border:1px solid #eee; padding:12px; background:#fafafa;">
                        <div class="text-muted small">ابدأ بطرح سؤال أو استخدم زر الصوت للتحدث.</div>
                    </div>

                    <div class="mt-3 d-flex gap-2">
                        <textarea id="messageInput" class="form-control" rows="2" placeholder="اكتب سؤالك هنا"></textarea>
                        <div class="d-flex flex-column">
                            <button id="sendBtn" class="btn btn-success mb-2">إرسال</button>
                            <button id="voiceBtn" class="btn btn-outline-primary">🔊 تكلم</button>
                        </div>
                    </div>

                    <div class="mt-2 text-muted small">المساعد يجيب بناءً على محتوى الدورة فقط. لا تشارك حلول الاختبارات.</div>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header bg-light">خيارات المساعد</div>
                <div class="card-body">
                    <button id="btnSummarize" class="btn btn-outline-secondary w-100 mb-2">تلخيص نص مُلصق</button>
                    <textarea id="summarizeInput" class="form-control mb-2" rows="4" placeholder="ألصق نصاً هنا للتلخيص"></textarea>
                    <div id="ai_alert"></div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
const chatBox = document.getElementById('chatBox');
const messageInput = document.getElementById('messageInput');
const sendBtn = document.getElementById('sendBtn');
const voiceBtn = document.getElementById('voiceBtn');
const btnSummarize = document.getElementById('btnSummarize');
const summarizeInput = document.getElementById('summarizeInput');

let recognition; let recognizing = false;

function appendMessage(text, from='assistant'){
    const wrapper = document.createElement('div');
    wrapper.className = 'mb-3';
    const inner = document.createElement('div');
    inner.style.maxWidth = '85%';
    inner.style.padding = '8px';
    inner.style.borderRadius = '8px';
    if(from === 'user'){
        inner.style.background = '#0d6efd'; inner.style.color = '#fff'; inner.innerHTML = '<strong>أنت:</strong><div>'+escapeHtml(text)+'</div>';
        wrapper.style.textAlign = 'right';
    } else {
        inner.style.background = '#fff'; inner.style.color = '#000'; inner.innerHTML = '<strong>المساعد:</strong><div>'+escapeHtml(text)+'</div>';
    }
    wrapper.appendChild(inner);
    chatBox.appendChild(wrapper);
    chatBox.scrollTop = chatBox.scrollHeight;
}

function escapeHtml(unsafe){ return (unsafe+'').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;').replace(/'/g,'&#039;'); }

async function sendMessage(text){
    if(!text || !text.trim()) return;
    appendMessage(text,'user');
    messageInput.value = '';
    appendMessage('...جاري استدعاء المساعد','assistant');
    try{
        const res = await fetch("<?php echo e(route('student.courses.ai.ask', $course)); ?>",{
            method:'POST', headers:{'Content-Type':'application/json','X-CSRF-TOKEN':'<?php echo e(csrf_token()); ?>'},
            body: JSON.stringify({ message: text })
        });
        const json = await res.json();
        // remove last loading assistant message
        const nodes = chatBox.querySelectorAll('div.mb-3');
        if(nodes.length) nodes[nodes.length-1].remove();
        if(json.error){ appendMessage(json.error,'assistant'); speakText(json.error); return; }
        const answer = json.answer || json.data?.answer || 'عذراً، لم أتمكن من الحصول على رد.';
        appendMessage(answer,'assistant');
        speakText(answer);
    }catch(e){ appendMessage('حدث خطأ في الاتصال بالخادم.','assistant'); }
}

sendBtn.addEventListener('click', ()=> sendMessage(messageInput.value));
messageInput.addEventListener('keydown', (e)=>{ if(e.key==='Enter' && !e.shiftKey){ e.preventDefault(); sendMessage(messageInput.value); }});

function speakText(text){ if(!('speechSynthesis' in window)) return; const u=new SpeechSynthesisUtterance(text); u.lang='ar-SA'; window.speechSynthesis.cancel(); window.speechSynthesis.speak(u); }

// Speech recognition
if('webkitSpeechRecognition' in window || 'SpeechRecognition' in window){
    const SpeechRec = window.SpeechRecognition || window.webkitSpeechRecognition;
    recognition = new SpeechRec(); recognition.lang = 'ar-SA'; recognition.interimResults = false; recognition.maxAlternatives = 1;
    recognition.onstart = ()=>{ recognizing=true; voiceBtn.innerText='⏺️ إيقاف'; };
    recognition.onend = ()=>{ recognizing=false; voiceBtn.innerText='🔊 تكلم'; };
    recognition.onerror = ()=>{ recognizing=false; voiceBtn.innerText='🔊 تكلم'; };
    recognition.onresult = (e)=>{ const t = e.results[0][0].transcript; messageInput.value = t; sendMessage(t); };
    voiceBtn.addEventListener('click', ()=>{ if(recognizing){ recognition.stop(); } else { try{ recognition.start(); }catch(err){ console.error(err); } } });
} else { voiceBtn.disabled=true; voiceBtn.title='متصفحك لا يدعم الإدخال الصوتي'; }

btnSummarize.addEventListener('click', ()=>{
    const content = summarizeInput.value.trim(); if(!content){ document.getElementById('ai_alert').innerHTML = '<div class="alert alert-warning">ألصق نصاً أولاً.</div>'; return; }
    sendMessage('تلخيص النص التالي:\n\n' + content);
});

</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.student', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\himm23\resources\views/student/ai/assistant.blade.php ENDPATH**/ ?>