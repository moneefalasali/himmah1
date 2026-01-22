@props(['lesson'])

<div class="relative group overflow-hidden bg-black rounded-lg shadow-lg" id="video-container-{{ $lesson->id }}">
    <video id="video-{{ $lesson->id }}" class="w-full aspect-video" controls crossorigin playsinline></video>
    
    <!-- Dynamic Watermark -->
    <div id="watermark-{{ $lesson->id }}" class="absolute pointer-events-none select-none text-white opacity-30 text-sm font-bold z-50 transition-all duration-500">
        {{ auth()->user()->email }} | {{ now()->format('Y-m-d H:i') }}
    </div>

    <!-- Protection Overlay (Blur/Blackout) -->
    <div id="protection-overlay-{{ $lesson->id }}" class="absolute inset-0 bg-black hidden z-40 flex items-center justify-center text-white text-center p-4">
        <p>تم إيقاف العرض لحماية المحتوى. يرجى العودة لمتصفحك.</p>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/hls.js@latest"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const video = document.getElementById('video-{{ $lesson->id }}');
    const container = document.getElementById('video-container-{{ $lesson->id }}');
    const watermark = document.getElementById('watermark-{{ $lesson->id }}');
    const overlay = document.getElementById('protection-overlay-{{ $lesson->id }}');
    const lessonId = {{ $lesson->id }};

    // 1. Initialize HLS
    if (Hls.isSupported()) {
        const hls = new Hls({
            xhrSetup: function(xhr, url) {
                xhr.withCredentials = true; // For session cookies
            }
        });

        fetch(`/api/lessons/${lessonId}/stream-url`)
            .then(response => response.json())
            .then(data => {
                if (data.stream_url) {
                    hls.loadSource(data.stream_url);
                    hls.attachMedia(video);
                }
            });
    } else if (video.canPlayType('application/vnd.apple.mpegurl')) {
        fetch(`/api/lessons/${lessonId}/stream-url`)
            .then(response => response.json())
            .then(data => {
                video.src = data.stream_url;
            });
    }

    // 2. Dynamic Watermark Movement
    function moveWatermark() {
        const x = Math.random() * (container.clientWidth - watermark.clientWidth);
        const y = Math.random() * (container.clientHeight - watermark.clientHeight);
        watermark.style.left = x + 'px';
        watermark.style.top = y + 'px';
    }
    setInterval(moveWatermark, 25000); // Move every 25 seconds
    moveWatermark();

    // 3. Protection: Random Noise/Blur to disrupt recording
    function applyRandomNoise() {
        if (!video.paused) {
            const shouldBlur = Math.random() > 0.95; // 5% chance every interval
            if (shouldBlur) {
                container.style.filter = 'contrast(150%) brightness(80%) blur(1px)';
                setTimeout(() => {
                    container.style.filter = 'none';
                }, 500);
            }
        }
    }
    setInterval(applyRandomNoise, 5000);

    // 4. Protection: Blur on Focus Loss
    window.addEventListener('blur', () => {
        video.pause();
        overlay.classList.remove('hidden');
        container.style.filter = 'blur(20px)';
    });

    window.addEventListener('focus', () => {
        overlay.classList.add('hidden');
        container.style.filter = 'none';
    });

    // 4. Protection: Visibility Change
    document.addEventListener('visibilitychange', () => {
        if (document.hidden) {
            video.pause();
        }
    });

    // 5. Prevent Right Click
    container.addEventListener('contextmenu', e => e.preventDefault());

    // 6. Audit Logging (Simple version)
    video.addEventListener('play', () => {
        fetch('/api/video/log', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
            body: JSON.stringify({ lesson_id: lessonId, action: 'play', time: video.currentTime })
        });
    });
});
</script>
