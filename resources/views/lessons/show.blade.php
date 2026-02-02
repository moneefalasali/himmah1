@extends('layouts.app')

@section('title', $lesson->title)

@section('content')
<div class="container my-5">
    <div class="row g-4">
        <!-- Video column -->
        <div class="col-12 col-lg-8">
            <div class="d-flex justify-content-between align-items-start mb-3">
                <div>
                    <h2 class="h4 mb-0">{{ $lesson->course->title }}</h2>
                    <div class="text-muted small">بواسطة {{ $lesson->course->teacher?->name }} • {{ $lesson->course->university?->name ?? '' }}</div>
                </div>
                <div class="text-end">
                    <div class="fw-bold">{{ $lesson->course->price > 0 ? $lesson->course->price . ' ريال' : 'مجاني' }}</div>
                </div>
            </div>

            @php
                $user = auth()->user();
                $canView = false;
                if ($user) {
                    if ($user->isAdmin()) $canView = true;
                    if ($user->isTeacher() && $lesson->course->user_id === $user->id) $canView = true;
                    if ($user->isEnrolledIn($lesson->course)) $canView = true;
                }
            @endphp

            <div class="ratio ratio-16x9 bg-dark rounded shadow-sm overflow-hidden">
                @if($canView)
                    @php
                        $videoService = app(\App\Services\VideoService::class);
                    @endphp

                    @if($lesson->video_platform === 'wasabi')
                        @php
                            $hlsReady = ($lesson->processing_status === 'completed' && !empty($lesson->hls_path));
                        @endphp

                        @if($hlsReady || !empty($lesson->video_path))
                            <link href="https://vjs.zencdn.net/7.20.3/video-js.css" rel="stylesheet" />

                            <style>
                                .lesson-player .video-js { width:100%; height:100%; }
                                .player-toolbar { position: absolute; top: 8px; right: 8px; z-index: 6; display:flex; gap:8px; align-items:center; }
                                .player-container { position: relative; width:100%; height:100%; }
                                .watermark-overlay { position:absolute; bottom:8px; left:8px; z-index:6; color:rgba(255,255,255,0.8); font-size:12px; }
                                .player-controls-btn { min-width:36px; }
                            </style>

                            <div class="player-container lesson-player" data-video-source="wasabi" data-stream-endpoint="{{ route('lessons.stream_url', $lesson) }}" data-hls-ready="{{ $hlsReady ? 1 : 0 }}">
                                <div class="ratio ratio-16x9 bg-dark rounded shadow-sm overflow-hidden">
                                    <video id="hls-player" class="video-js vjs-big-play-centered vjs-fluid" controls preload="auto" playsinline muted autoplay controlsList="nodownload nofullscreen noremoteplayback" disablePictureInPicture oncontextmenu="return false;"></video>
                                </div>

                                <div class="player-toolbar">
                                    <button id="back10" class="btn btn-sm btn-outline-light player-controls-btn">⏪ 10</button>
                                    <button id="forward10" class="btn btn-sm btn-outline-light player-controls-btn">10 ⏩</button>
                                    <select id="playbackRate" class="form-select form-select-sm">
                                        <option value="0.5">0.5x</option>
                                        <option value="0.75">0.75x</option>
                                        <option value="1" selected>1x</option>
                                        <option value="1.25">1.25x</option>
                                        <option value="1.5">1.5x</option>
                                        <option value="2">2x</option>
                                    </select>
                                    <button id="cinemaToggle" class="btn btn-sm btn-outline-light">وضع السينما</button>
                                </div>

                                <div id="playerWatermark" class="watermark-overlay" aria-hidden="true"></div>
                            </div>

                            <script src="https://vjs.zencdn.net/7.20.3/video.min.js"></script>
                            <script src="https://cdn.jsdelivr.net/npm/hls.js@1.4.0/dist/hls.min.js"></script>

                            <script>
                                // Unified playback controls for Wasabi HLS via Video.js + hls.js
                                (function(){
                                    const container = document.querySelector('.player-container[data-video-source="wasabi"]');
                                    if (!container) return;

                                    const streamEndpoint = container.dataset.streamEndpoint;
                                    const isHls = container.dataset.hlsReady === '1';
                                    const player = videojs('hls-player', { controls: true, fluid: true, preload: 'auto', autoplay: true, muted: true });

                                    // Protection: do not place signed URLs in page source; fetch them from server
                                    async function fetchStreamUrl() {
                                        try {
                                            const res = await fetch(streamEndpoint, { credentials: 'same-origin' });
                                            if (!res.ok) throw new Error('Failed to get stream URL');
                                            return await res.json();
                                        } catch (err) {
                                            console.error('Unable to fetch stream URL', err);
                                            return null;
                                        }
                                    }

                                    async function init() {
                                        const data = await fetchStreamUrl();
                                        if (!data || !data.stream_url) {
                                            container.querySelector('.ratio').innerHTML = '<div class="d-flex align-items-center justify-content-center h-100 text-white p-3"><p>الفيديو قيد المعالجة أو غير متاح حالياً.</p></div>';
                                            return;
                                        }

                                        const src = data.stream_url;
                                        // Watermark text from server (keeps protection intact; server stamps or provides allowed info)
                                        if (data.watermark && data.watermark.text) {
                                            const wm = document.getElementById('playerWatermark');
                                            wm.textContent = data.watermark.text;
                                        }

                                        player.ready(function() {
                                            let tech = null;
                                            try { tech = player.tech({ IWillNotUseThisInPlugins: true }); } catch(e) { tech = null; }
                                            const videoEl = tech && typeof tech.el === 'function' ? tech.el() : document.getElementById('hls-player');

                                            if (isHls) {
                                                if (window.Hls && Hls.isSupported()) {
                                                    const hls = new Hls({ enableWorker: true });
                                                    hls.on(Hls.Events.ERROR, function(event, data) { console.error('HLS error', data); });
                                                    hls.loadSource(src);
                                                    hls.attachMedia(videoEl);
                                                    hls.on(Hls.Events.MANIFEST_PARSED, function() { try { player.src({ src: src, type: 'application/x-mpegURL' }); } catch(e) {} });
                                                } else if (videoEl && videoEl.canPlayType && (videoEl.canPlayType('application/vnd.apple.mpegurl') || videoEl.canPlayType('application/x-mpegURL'))) {
                                                    player.src({ src: src, type: 'application/x-mpegURL' });
                                                } else {
                                                    console.error('HLS requested but not supported by browser');
                                                    player.src({ src: src, type: 'video/mp4' });
                                                }
                                            } else {
                                                player.src({ src: src, type: 'video/mp4' });
                                            }

                                            // Try autoplay (muted) safely
                                            try { player.muted(true); } catch(e) {}
                                            try { const p = player.play && player.play(); if (p && typeof p.then === 'function') p.catch(()=>{}); } catch(e) {}
                                        });

                                        // Wire unified controls
                                        setupUnifiedControls(player, 'wasabi');
                                    }

                                    init();

                                    // Prevent right-click and long-press
                                    container.addEventListener('contextmenu', e => e.preventDefault());
                                    let touchTimer = null;
                                    container.addEventListener('touchstart', () => { touchTimer = setTimeout(()=>{}, 700); });
                                    container.addEventListener('touchend', () => { if (touchTimer) clearTimeout(touchTimer); });
                                })();
                            </script>
                        @else
                            <div class="d-flex align-items-center justify-content-center h-100 text-white p-3">
                                <p>الفيديو قيد المعالجة أو غير متاح حالياً.</p>
                            </div>
                        @endif

                    @elseif($lesson->video_platform === 'vimeo')
                        <div class="player-container" data-video-source="vimeo" data-vimeo-id="{{ $lesson->vimeo_video_id }}">
                            <div class="ratio ratio-16x9 bg-dark rounded shadow-sm overflow-hidden">
                                    <iframe id="vimeo-player" src="https://player.vimeo.com/video/{{ $lesson->vimeo_video_id }}?api=1&background=0&dnt=1" class="w-100 h-100" frameborder="0" allow="autoplay; fullscreen" allowfullscreen></iframe>
                                </div>
                                <script src="https://player.vimeo.com/api/player.js"></script>
                            <div class="player-toolbar">
                                <button id="back10" class="btn btn-sm btn-outline-light player-controls-btn">⏪ 10</button>
                                <button id="forward10" class="btn btn-sm btn-outline-light player-controls-btn">10 ⏩</button>
                                <select id="playbackRate" class="form-select form-select-sm">
                                    <option value="0.5">0.5x</option>
                                    <option value="0.75">0.75x</option>
                                    <option value="1" selected>1x</option>
                                    <option value="1.25">1.25x</option>
                                    <option value="1.5">1.5x</option>
                                    <option value="2">2x</option>
                                </select>
                                <button id="cinemaToggle" class="btn btn-sm btn-outline-light">وضع السينما</button>
                            </div>
                        </div>

                    @elseif($lesson->video_platform === 'google_drive')
                        @php
                            $driveFileId = $lesson->video_url ?: $lesson->video_path;
                            $driveUrl = route('admin.video.drive.proxy', ['fileId' => $driveFileId]);
                        @endphp
                        <div class="player-container" data-video-source="drive">
                            <div class="ratio ratio-16x9 bg-dark rounded shadow-sm overflow-hidden">
                                <video id="drive-player" class="w-100 h-100" controls controlsList="nodownload nofullscreen noremoteplayback" disablePictureInPicture oncontextmenu="return false;">
                                    <source src="{{ $driveUrl }}" type="video/mp4">
                                    Your browser does not support the video tag.
                                </video>
                            </div>
                            <div class="player-toolbar">
                                <button id="back10" class="btn btn-sm btn-outline-light player-controls-btn">⏪ 10</button>
                                <button id="forward10" class="btn btn-sm btn-outline-light player-controls-btn">10 ⏩</button>
                                <select id="playbackRate" class="form-select form-select-sm">
                                    <option value="0.5">0.5x</option>
                                    <option value="0.75">0.75x</option>
                                    <option value="1" selected>1x</option>
                                    <option value="1.25">1.25x</option>
                                    <option value="1.5">1.5x</option>
                                    <option value="2">2x</option>
                                </select>
                                <button id="cinemaToggle" class="btn btn-sm btn-outline-light">وضع السينما</button>
                            </div>
                        </div>
                    @else
                        <div class="d-flex align-items-center justify-content-center h-100 text-white">
                            <p>الفيديو غير متاح حالياً</p>
                        </div>
                    @endif

                    <script>
                        // Unified playback helpers
                        // Note: These controls do not reduce server-side protection. Signed URLs remain short-lived and/or proxied by the backend.
                        // Drive limitations: playbackRate and precise seeking depend on browser and the proxied MP4; some browsers or Drive-hosted streams may not support setPlaybackRate.

                        async function setPlaybackSpeedFor(source, player, rate) {
                            try {
                                if (source === 'wasabi') {
                                    // Video.js
                                    if (player && typeof player.playbackRate === 'function') player.playbackRate(rate);
                                    else if (player && player.tech && player.tech().el) player.tech().el().playbackRate = rate;
                                } else if (source === 'vimeo') {
                                    // Vimeo returns a Promise
                                    await player.setPlaybackRate(rate).catch(()=>{});
                                } else if (source === 'drive') {
                                    // HTML5 video
                                    if (player && typeof player.playbackRate !== 'undefined') player.playbackRate = rate;
                                }
                            } catch (e) { console.debug('setPlaybackSpeed error', e); }
                        }

                        async function skipBy(source, player, seconds) {
                            try {
                                if (source === 'wasabi') {
                                    if (!player) return;
                                    const cur = player.currentTime();
                                    player.currentTime(Math.max(0, cur + seconds));
                                } else if (source === 'vimeo') {
                                    const cur = await player.getCurrentTime();
                                    await player.setCurrentTime(Math.max(0, cur + seconds));
                                } else if (source === 'drive') {
                                    if (!player) return;
                                    player.currentTime = Math.max(0, player.currentTime + seconds);
                                }
                            } catch (e) { console.debug('skip error', e); }
                        }

                        // Wire controls inside a specific container
                        function setupUnifiedControls(playerInstance, sourceType) {
                            // find container for this source
                            const container = document.querySelector('.player-container[data-video-source="'+sourceType+'"], .player-container[data-video-source]') || document.querySelector('[data-video-source="'+sourceType+'"]');
                            if (!container) return;

                            // Elements (may be duplicated for multiple containers; prefer scoped queries)
                            const backBtn = container.querySelector('#back10');
                            const fwdBtn = container.querySelector('#forward10');
                            const rateSel = container.querySelector('#playbackRate');
                            const cinemaBtn = container.querySelector('#cinemaToggle');

                            if (backBtn) backBtn.addEventListener('click', (e) => { e.preventDefault(); skipBy(sourceType, playerInstance, -10); });
                            if (fwdBtn) fwdBtn.addEventListener('click', (e) => { e.preventDefault(); skipBy(sourceType, playerInstance, 10); });
                            if (rateSel) rateSel.addEventListener('change', (e) => { setPlaybackSpeedFor(sourceType, playerInstance, parseFloat(e.target.value)); });

                            if (cinemaBtn) cinemaBtn.addEventListener('click', (e) => {
                                const el = document.documentElement;
                                if (!el.classList.contains('cinema-mode')) { el.classList.add('cinema-mode'); cinemaBtn.textContent = 'خروج من السينما'; }
                                else { el.classList.remove('cinema-mode'); cinemaBtn.textContent = 'وضع السينما'; }
                                setTimeout(()=>{ try { if (sourceType === 'wasabi') { const p = videojs('hls-player'); if (p && typeof p.resize === 'function') p.resize(); else if (p && typeof p.trigger === 'function') p.trigger('resize'); } } catch(e){} }, 200);
                            });

                            // Keyboard shortcuts
                            document.addEventListener('keydown', async (e) => {
                                if (['INPUT','TEXTAREA'].includes(document.activeElement.tagName)) return;
                                if (e.code === 'Space') { e.preventDefault();
                                    try {
                                        if (sourceType === 'wasabi') { const p = videojs('hls-player'); if (p.paused()) p.play(); else p.pause(); }
                                        else if (sourceType === 'vimeo') { const st = await playerInstance.getPaused(); if (st) playerInstance.play(); else playerInstance.pause(); }
                                        else if (sourceType === 'drive') { if (playerInstance.paused) playerInstance.play(); else playerInstance.pause(); }
                                    } catch(e){}
                                }
                                if (e.code === 'ArrowRight') { skipBy(sourceType, playerInstance, 10); }
                                if (e.code === 'ArrowLeft') { skipBy(sourceType, playerInstance, -10); }
                            });
                        }

                        // Initialize Vimeo and Drive players and attach unified controls
                        document.addEventListener('DOMContentLoaded', function(){
                            // Vimeo
                            const vimeoContainer = document.querySelector('.player-container[data-video-source="vimeo"]');
                            if (vimeoContainer) {
                                const iframe = document.getElementById('vimeo-player');
                                if (iframe && window.Vimeo) {
                                    const vplayer = new Vimeo.Player(iframe);
                                    // Disable download via player parameters / privacy — embedding domain controls are enforced server-side.
                                    setupUnifiedControls(vplayer, 'vimeo');
                                    // Ensure right-click disabled
                                    iframe.addEventListener('contextmenu', e=>e.preventDefault());
                                }
                            }

                            // Drive (HTML5 video)
                            const driveContainer = document.querySelector('.player-container[data-video-source="drive"]');
                            if (driveContainer) {
                                const v = document.getElementById('drive-player');
                                if (v) {
                                    // Some browsers may not support playbackRate on streamed/proxied content
                                    setupUnifiedControls(v, 'drive');
                                    v.addEventListener('contextmenu', e=>e.preventDefault());
                                }
                            }
                        });
                    </script>
                @else
                    <div class="d-flex flex-column align-items-center justify-content-center h-100 text-white p-4">
                        <h3 class="mb-3">الفيديو متاح فقط للطلاب المسجلين في الدورة</h3>
                        <div>
                            @if(!auth()->check())
                                <a href="{{ route('login') }}?redirect={{ urlencode(url()->full()) }}" class="btn btn-primary me-2">تسجيل / تسجيل الدخول</a>
                            @else
                                @if($lesson->course->price > 0)
                                    <a href="{{ route('payment.form', $lesson->course) }}" class="btn btn-success me-2">اشترك في الدورة</a>
                                @else
                                    <form action="{{ route('courses.attach', $lesson->course) ?? '#' }}" method="POST" style="display:inline">
                                        @csrf
                                        <button class="btn btn-success me-2">انضم إلى الدورة</button>
                                    </form>
                                @endif
                            @endif
                            <a href="{{ env('PLATFORM_WHATSAPP') ? env('PLATFORM_WHATSAPP') : 'mailto:' . 'hemmah.platform.app@gmail.com' }}" target="_blank" class="btn btn-outline-light">طلب مساعدة</a>
                        </div>
                    </div>
                @endif
            </div>

            <div class="mt-4 bg-white p-4 rounded shadow-sm">
                <h1 class="h5 mb-2">{{ $lesson->title }}</h1>
                <p class="text-muted">{{ $lesson->description }}</p>
            </div>
        </div>

        <!-- Sidebar: lessons list -->
        <div class="col-12 col-lg-4">
            <div class="card shadow-sm" style="position:sticky; top:24px;">
                <div class="card-header bg-white">
                    <h6 class="mb-0">محتوى الدورة</h6>
                </div>
                <div class="list-group list-group-flush" style="max-height:70vh; overflow-y:auto;">
                    @foreach($lesson->course->lessons as $l)
                        <a href="{{ route('lessons.show', $l) }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center {{ $l->id === $lesson->id ? 'active' : '' }}">
                            <div class="d-flex align-items-center">
                                <div class="me-3">
                                    @if($l->id === $lesson->id)
                                        <i class="fas fa-play-circle text-white"></i>
                                    @else
                                        <i class="far fa-play-circle text-muted"></i>
                                    @endif
                                </div>
                                <div>
                                    <div class="fw-medium">{{ $l->title }}</div>
                                    <div class="small text-muted">{{ Str::limit($l->description ?? '', 60) }}</div>
                                </div>
                            </div>
                            <small class="text-muted">{{ $l->duration }} دقيقة</small>
                        </a>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
