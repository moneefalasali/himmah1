<div id="ai-assistant" class="fixed bottom-4 right-4 w-80 bg-white shadow-xl rounded-lg border border-gray-200 hidden">
    <div class="bg-blue-600 text-white p-3 rounded-t-lg flex justify-between items-center">
        <span>المساعد الذكي</span>
        <button onclick="toggleAI()" class="text-white">&times;</button>
    </div>
    <div id="ai-messages" class="h-64 overflow-y-auto p-3 space-y-2 text-sm">
        <div class="bg-gray-100 p-2 rounded">مرحباً! أنا مساعدك الذكي في هذا الكورس. كيف يمكنني مساعدتك اليوم؟</div>
    </div>
    <div class="p-3 border-t">
        <div class="flex gap-2">
            <input type="text" id="ai-input" class="flex-1 border rounded px-2 py-1 text-sm" placeholder="اسأل شيئاً...">
            <button onclick="sendToAI()" class="bg-blue-600 text-white px-3 py-1 rounded text-sm">إرسال</button>
        </div>
    </div>
</div>

<button onclick="toggleAI()" class="fixed bottom-4 right-4 bg-blue-600 text-white p-3 rounded-full shadow-lg">
    <i class="fas fa-robot"></i> AI
</button>

<script>
function toggleAI() {
    document.getElementById('ai-assistant').classList.toggle('hidden');
}

async function sendToAI() {
    const input = document.getElementById('ai-input');
    const messages = document.getElementById('ai-messages');
    const text = input.value;
    if(!text) return;

    // إضافة رسالة المستخدم
    messages.innerHTML += `<div class="bg-blue-50 p-2 rounded text-right">${text}</div>`;
    input.value = '';

    try {
        const response = await fetch('{{ route("student.ai.chat", $course->id) }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ message: text })
        });
        const data = await response.json();
        
        if(data.answer) {
            messages.innerHTML += `<div class="bg-gray-100 p-2 rounded">${data.answer}</div>`;
        } else {
            messages.innerHTML += `<div class="bg-red-50 p-2 rounded text-red-600">${data.error}</div>`;
        }
        messages.scrollTop = messages.scrollHeight;
    } catch (e) {
        messages.innerHTML += `<div class="bg-red-50 p-2 rounded text-red-600">حدث خطأ في الاتصال.</div>`;
    }
}
</script>
