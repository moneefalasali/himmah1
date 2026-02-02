@props(['lesson'])

<div class="relative group overflow-hidden bg-black rounded-lg shadow-lg" id="video-container-{{ $lesson->id }}">
    <!-- Video element for HLS/HTML5 sources. `controlsList` and `disablePictureInPicture` help prevent easy downloading/PIP. -->
    <style>
        /* Minimal protection UI styles */
        .no-drag { -webkit-user-drag: none; -webkit-user-select: none; user-select: none; }
    </style>
    <video id="video-{{ $lesson->id }}" class="w-full aspect-video no-drag" controls crossorigin playsinline controlsList="nodownload" disablepictureinpicture oncontextmenu="event.preventDefault();return false;" ondragstart="return false;" onselectstart="return false;" ></video>
    
    <!-- Dynamic Watermark -->
    <div id="watermark-{{ $lesson->id }}" class="absolute pointer-events-none select-none text-white opacity-30 text-sm font-bold z-50 transition-all duration-500">
        {{ auth()->user()->email }} | {{ now()->format('Y-m-d H:i') }}
    </div>

    <!-- Protection Overlay (Blur/Blackout) -->
    <div id="protection-overlay-{{ $lesson->id }}" class="absolute inset-0 bg-black hidden z-40 flex items-center justify-center text-white text-center p-4">
        <p>تم إيقاف العرض لحماية المحتوى. يرجى العودة لمتصفحك.</p>
    </div>

    <!-- Unified Controls (Speed + Skip) -->
    <div id="player-controls-{{ $lesson->id }}" class="absolute left-0 right-0 bottom-0 z-50 p-2 bg-gradient-to-t from-black/70 to-transparent flex items-center gap-2">
        <button id="skip-back-{{ $lesson->id }}" aria-label="Back 10 seconds" class="px-2 py-1 bg-white/10 text-white rounded">⏪ 10</button>
        <button id="skip-forward-{{ $lesson->id }}" aria-label="Forward 10 seconds" class="px-2 py-1 bg-white/10 text-white rounded">10 ⏩</button>
        <label for="speed-{{ $lesson->id }}" class="sr-only">Playback speed</label>
        <select id="speed-{{ $lesson->id }}" class="ml-2 bg-white/10 text-white rounded px-2 py-1">
            <option value="0.5">0.5x</option>
            <option value="0.75">0.75x</option>
            <option value="1" selected>1x</option>
            <option value="1.25">1.25x</option>
            <option value="1.5">1.5x</option>
            <option value="2">2x</option>
        </select>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/hls.js@latest"></script>
<script src="https://player.vimeo.com/api/player.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const video = document.getElementById('video-{{ $lesson->id }}');
    const container = document.getElementById('video-container-{{ $lesson->id }}');
    const watermark = document.getElementById('watermark-{{ $lesson->id }}');
    const overlay = document.getElementById('protection-overlay-{{ $lesson->id }}');
    const lessonId = {{ $lesson->id }};
    const controls = document.getElementById('player-controls-{{ $lesson->id }}');
    const skipBackBtn = document.getElementById('skip-back-{{ $lesson->id }}');
    const skipForwardBtn = document.getElementById('skip-forward-{{ $lesson->id }}');
    const speedSelect = document.getElementById('speed-{{ $lesson->id }}');

    // Source detection / runtime objects
    let sourceType = 'hls'; // 'hls' | 'vimeo' | 'drive'
    let hlsInstance = null;
    let vimeoPlayer = null;
    let driveIframe = null;

    // 1. Initialize playback source (HLS/Wasabi, Vimeo, Google Drive)
    fetch(`/api/lessons/${lessonId}/stream-url`)
        .then(response => response.json())
        .then(data => {
            // `data.stream_url` is expected from backend. We detect provider by URL pattern.
            const url = data.stream_url || '';
            if (!url) return;

            // Vimeo embed detection
            if (url.includes('vimeo.com') || url.includes('player.vimeo.com')) {
                sourceType = 'vimeo';
                setupVimeo(url);
                return;
            }

            // Google Drive detection
            if (url.includes('drive.google.com')) {
                sourceType = 'drive';
                setupDrive(url);
                return;
            }

            // Default: treat as HLS (Wasabi) source
            sourceType = 'hls';
            if (Hls.isSupported()) {
                hlsInstance = new Hls({
                    xhrSetup: function(xhr, url) { xhr.withCredentials = true; }
                });
                hlsInstance.loadSource(url);
                hlsInstance.attachMedia(video);
            } else if (video.canPlayType('application/vnd.apple.mpegurl')) {
                video.src = url;
            }
        });

    function setupVimeo(embedUrl) {
        // Replace the existing video element with an iframe for Vimeo
        const iframe = document.createElement('iframe');
        iframe.id = 'vimeo-iframe-{{ $lesson->id }}';
        iframe.src = embedUrl;
        iframe.allow = 'autoplay; encrypted-media; picture-in-picture';
        iframe.setAttribute('allowfullscreen', '');
        iframe.className = 'w-full aspect-video';

        // Hide native video element
        video.style.display = 'none';
        container.insertBefore(iframe, container.firstChild);

        // Initialize Vimeo Player API
        vimeoPlayer = new Vimeo.Player(iframe);

        // Disable context menu on iframe container
        iframe.addEventListener('load', () => {
            try { iframe.contentWindow.document.addEventListener('contextmenu', e => e.preventDefault()); } catch(e) { /* cross-origin; ignore */ }
        });
    }

    function setupDrive(embedUrl) {
        // Drive often provides an embed iframe. We'll place it and try to control where allowed.
        const iframe = document.createElement('iframe');
        iframe.id = 'drive-iframe-{{ $lesson->id }}';
        iframe.src = embedUrl;
        iframe.className = 'w-full aspect-video bg-black';
        iframe.allow = 'autoplay; encrypted-media';
        iframe.setAttribute('allowfullscreen', '');

        video.style.display = 'none';
        container.insertBefore(iframe, container.firstChild);
        driveIframe = iframe;

        // Note: Google Drive iframe is cross-origin; controlling playbackRate or currentTime
        // may not be supported. We'll attempt postMessage-based control later if possible.
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

    // Prevent long-press context menus on touch devices and harden right-click prevention
    let touchstartY = 0;
    container.addEventListener('touchstart', (e) => { if (e.touches && e.touches[0]) touchstartY = e.touches[0].clientY; });
    container.addEventListener('touchmove', (e) => { touchstartY = 0; });

    // Prevent contextmenu when the event originates from inside the player container.
    // Use capture to intercept before the browser shows native menus.
    function eventPathIncludesContainer(e) {
        try {
            if (e.composedPath) {
                return e.composedPath().includes(container);
            }
        } catch (err) {}
        // Fallback: walk up the DOM
        let el = e.target;
        while (el) {
            if (el === container) return true;
            el = el.parentElement;
        }
        return false;
    }

    document.addEventListener('contextmenu', function(e) {
        if (eventPathIncludesContainer(e)) {
            e.preventDefault();
        }
    }, true);

    // Also block secondary-button mousedown on video to prevent some browser behaviours
    video.addEventListener('mousedown', function(e) {
        if (e.button === 2) e.preventDefault();
    }, true);

    // Intercept common save shortcuts when focus is inside the player
    document.addEventListener('keydown', function(e) {
        if (!eventPathIncludesContainer(e)) return;
        if ((e.ctrlKey || e.metaKey) && e.key.toLowerCase() === 's') {
            e.preventDefault();
        }
    }, true);

    // 5. Prevent Right Click
    container.addEventListener('contextmenu', e => e.preventDefault());

    // 6. Audit Logging (Simple version)
    function logAction(action, time) {
        fetch('/api/video/log', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
            body: JSON.stringify({ lesson_id: lessonId, action, time })
        }).catch(()=>{});
    }

    if (video) {
        video.addEventListener('play', () => logAction('play', video.currentTime));
    }

    // Unified control functions
    function setPlaybackSpeed(rate) {
        // Security note: Changing `playbackRate` is a client-side UX control and does not expose
        // direct URLs nor weaken server-side protections (signed URLs remain required server-side).
        if (sourceType === 'hls') {
            if (video) video.playbackRate = rate;
        } else if (sourceType === 'vimeo' && vimeoPlayer) {
            // Vimeo supports setPlaybackRate where account allows it. This may reject for some videos.
            vimeoPlayer.setPlaybackRate(rate).catch(() => {
                // Not all Vimeo endpoints permit playback rate changes; ignore failures gracefully.
            });
        } else if (sourceType === 'drive') {
            // Google Drive iframe: playbackRate control often not available for cross-origin embeds.
            // NOTE: Many Drive embeds will not accept JavaScript control; this is a limitation of Drive.
        }
    }

    function skipForward() {
        if (sourceType === 'hls') {
            if (video) { video.currentTime = Math.min(video.duration || Infinity, video.currentTime + 10); }
        } else if (sourceType === 'vimeo' && vimeoPlayer) {
            vimeoPlayer.getCurrentTime().then(t => vimeoPlayer.setCurrentTime(t + 10)).catch(()=>{});
        } else if (sourceType === 'drive' && driveIframe) {
            // Drive: try postMessage if the embed supports it. Many Drive embeds do not.
            try { driveIframe.contentWindow.postMessage({ method: 'seek', seconds: 10 }, '*'); } catch(e) { }
        }
    }

    function skipBackward() {
        if (sourceType === 'hls') {
            if (video) { video.currentTime = Math.max(0, video.currentTime - 10); }
        } else if (sourceType === 'vimeo' && vimeoPlayer) {
            vimeoPlayer.getCurrentTime().then(t => vimeoPlayer.setCurrentTime(Math.max(0, t - 10))).catch(()=>{});
        } else if (sourceType === 'drive' && driveIframe) {
            try { driveIframe.contentWindow.postMessage({ method: 'seek', seconds: -10 }, '*'); } catch(e) { }
        }
    }

    // Wire UI
    skipBackBtn.addEventListener('click', skipBackward);
    skipForwardBtn.addEventListener('click', skipForward);
    speedSelect.addEventListener('change', (e) => setPlaybackSpeed(Number(e.target.value)));

    // Keyboard shortcuts: J / L for back/forward, Shift+>/< for speed changes (common YouTube behaviour)
    document.addEventListener('keydown', (e) => {
        // avoid interfering with form inputs
        const active = document.activeElement;
        if (active && (active.tagName === 'INPUT' || active.tagName === 'TEXTAREA')) return;
        if (e.key === 'j' || e.key === 'J') { skipBackward(); }
        if (e.key === 'l' || e.key === 'L') { skipForward(); }
    });

    // Prevent download via drag/drop or context menu
    ['dragstart','drop'].forEach(evt => container.addEventListener(evt, e => e.preventDefault()));
});
</script>
