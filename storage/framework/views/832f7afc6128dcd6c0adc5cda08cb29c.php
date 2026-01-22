<?php $__env->startSection('title', $lesson->title); ?>

<?php $__env->startSection('content'); ?>
<div class="container my-5">
    <div class="row g-4">
        <!-- Video column -->
        <div class="col-12 col-lg-8">
            <div class="d-flex justify-content-between align-items-start mb-3">
                <div>
                    <h2 class="h4 mb-0"><?php echo e($lesson->course->title); ?></h2>
                    <div class="text-muted small">بواسطة <?php echo e($lesson->course->teacher?->name); ?> • <?php echo e($lesson->course->university?->name ?? ''); ?></div>
                </div>
                <div class="text-end">
                    <div class="fw-bold"><?php echo e($lesson->course->price > 0 ? $lesson->course->price . ' ريال' : 'مجاني'); ?></div>
                </div>
            </div>

            <?php
                $user = auth()->user();
                $canView = false;
                if ($user) {
                    if ($user->isAdmin()) $canView = true;
                    if ($user->isTeacher() && $lesson->course->user_id === $user->id) $canView = true;
                    if ($user->isEnrolledIn($lesson->course)) $canView = true;
                }
            ?>

            <div class="ratio ratio-16x9 bg-dark rounded shadow-sm overflow-hidden">
                <?php if($canView): ?>
                    <?php
                        $videoService = app(\App\Services\VideoService::class);
                    ?>

                    <?php if($lesson->video_platform === 'wasabi'): ?>
                            <?php
                            $hlsReady = ($lesson->processing_status === 'completed' && !empty($lesson->hls_path));
                            $videoUrl = null;
                            if ($hlsReady) {
                                $videoUrl = $videoService->getWasabiSignedUrl($lesson->hls_path);
                            } elseif (!empty($lesson->video_path)) {
                                $videoUrl = $videoService->getWasabiSignedUrl($lesson->video_path);
                            }
                            // Ensure no HTML entities (e.g., &amp;) remain in the signed URL
                            if (!empty($videoUrl)) {
                                $videoUrl = html_entity_decode($videoUrl, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
                            }
                        ?>

                        <?php if($videoUrl): ?>
                            <link href="https://vjs.zencdn.net/7.20.3/video-js.css" rel="stylesheet" />

                            <style>
                                /* Make player fill the available frame and show controls on hover */
                                .lesson-player .video-js { width:100%; height:100%; }
                                .lesson-player .vjs-control { opacity: 0; transition: opacity .15s ease; }
                                .lesson-player:hover .vjs-control, .lesson-player .vjs-control.vjs-hidden { opacity: 1; }
                                .player-toolbar { position: absolute; top: 8px; right: 8px; z-index: 6; }
                                .player-container { position: relative; width:100%; height:100%; }
                            </style>

                            <div class="player-container lesson-player">
                                    <div class="ratio ratio-16x9 bg-dark rounded shadow-sm overflow-hidden">
                                    <video id="hls-player" class="video-js vjs-big-play-centered vjs-fluid" controls preload="auto" playsinline muted autoplay>
                                        <p class="vjs-no-js">لمشاهدة هذا الفيديو، يرجى تفعيل JavaScript وتحديث المتصفح.</p>
                                    </video>
                                </div>
                                <div class="player-toolbar">
                                    <button id="cinemaToggle" class="btn btn-sm btn-outline-light">وضع السينما</button>
                                </div>
                            </div>

                            <script src="https://vjs.zencdn.net/7.20.3/video.min.js"></script>
                            <script src="https://cdn.jsdelivr.net/npm/hls.js@1.4.0/dist/hls.min.js"></script>
                            <script>
                                (function(){
                                    const src = <?php echo json_encode($videoUrl, 15, 512) ?>;
                                    const isHls = <?php echo json_encode($hlsReady, 15, 512) ?>;
                                       // Removed debug logging and on-page display of signed URL
                                    const player = videojs('hls-player', {
                                        controls: true,
                                        fluid: true,
                                        preload: 'auto',
                                        autoplay: true,
                                        muted: true
                                    });

                                    // Initialize when player ready to access the underlying video element
                                    player.ready(function() {
                                        let tech = null;
                                        try { tech = player.tech({ IWillNotUseThisInPlugins: true }); } catch(e) { tech = null; }
                                        const videoEl = tech && typeof tech.el === 'function' ? tech.el() : document.getElementById('hls-player');

                                        if (!src) {
                                            console.warn('No video URL provided');
                                            return;
                                        }

                                        // If this lesson provides HLS, use hls.js (or native HLS). Otherwise load the MP4 directly.
                                        if (isHls) {
                                            if (window.Hls && Hls.isSupported()) {
                                                const hls = new Hls();
                                                hls.on(Hls.Events.ERROR, function(event, data) { console.error('HLS error', data); });
                                                hls.loadSource(src);
                                                hls.attachMedia(videoEl);
                                                hls.on(Hls.Events.MANIFEST_PARSED, function() {
                                                    try { player.src({ src: src, type: 'application/x-mpegURL' }); } catch(e) {}
                                                });
                                            } else if (videoEl && videoEl.canPlayType && (videoEl.canPlayType('application/vnd.apple.mpegurl') || videoEl.canPlayType('application/x-mpegURL'))) {
                                                player.src({ src: src, type: 'application/x-mpegURL' });
                                            } else {
                                                console.error('HLS requested but not supported by browser');
                                                // fall back to MP4 if available
                                                player.src({ src: src, type: 'video/mp4' });
                                            }
                                        } else {
                                            // Non-HLS (MP4) — load directly
                                            player.src({ src: src, type: 'video/mp4' });
                                        }

                                        // Attempt autoplay (muted) and handle promise rejection
                                        try { player.muted(true); } catch(e) {}
                                        try {
                                            const p = player.play && player.play();
                                            if (p && typeof p.then === 'function') p.then(() => console.log('Autoplay started')).catch(err => console.debug('Autoplay prevented', err));
                                        } catch(e) { console.debug('autoplay attempt failed', e); }

                                        // Listen for playback errors
                                        player.on('error', function() {
                                            const err = player.error();
                                            console.error('Video.js error', err);
                                        });
                                    });

                                    // Cinema toggle => fullscreen-like experience
                                    const cinemaToggle = document.getElementById('cinemaToggle');
                                    cinemaToggle.addEventListener('click', () => {
                                        const el = document.documentElement;
                                        if (!el.classList.contains('cinema-mode')) {
                                            el.classList.add('cinema-mode');
                                            cinemaToggle.textContent = 'خروج من السينما';
                                        } else {
                                            el.classList.remove('cinema-mode');
                                            cinemaToggle.textContent = 'وضع السينما';
                                        }
                                        setTimeout(() => {
                                            try {
                                                const p = videojs('hls-player');
                                                if (p && typeof p.resize === 'function') p.resize();
                                                else if (p && typeof p.trigger === 'function') p.trigger('resize');
                                            } catch (e) { console.debug('resize fallback failed', e); }
                                        }, 200);
                                    });

                                    // Keyboard shortcuts: space play/pause, arrows seek
                                    document.addEventListener('keydown', (e) => {
                                        if (['INPUT','TEXTAREA'].includes(document.activeElement.tagName)) return;
                                        const p = videojs('hls-player');
                                        if (!p) return;
                                        if (e.code === 'Space') { e.preventDefault(); if (p.paused()) p.play(); else p.pause(); }
                                        if (e.code === 'ArrowRight') p.currentTime(p.currentTime() + 5);
                                        if (e.code === 'ArrowLeft') p.currentTime(p.currentTime() - 5);
                                    });
                                })();
                            </script>
                        <?php else: ?>
                            <div class="d-flex align-items-center justify-content-center h-100 text-white p-3">
                                <p>الفيديو قيد المعالجة أو غير متاح حالياً.</p>
                            </div>
                        <?php endif; ?>

                    <?php elseif($lesson->video_platform === 'vimeo'): ?>
                        <iframe src="https://player.vimeo.com/video/<?php echo e($lesson->vimeo_video_id); ?>" class="w-100 h-100" frameborder="0" allow="autoplay; fullscreen" allowfullscreen></iframe>

                    <?php elseif($lesson->video_platform === 'google_drive'): ?>
                        <?php
                            $driveFileId = $lesson->video_url ?: $lesson->video_path;
                            $driveUrl = route('admin.video.drive.proxy', ['fileId' => $driveFileId]);
                        ?>
                        <video id="drive-player" class="w-100 h-100" controls controlsList="nodownload">
                            <source src="<?php echo e($driveUrl); ?>" type="video/mp4">
                            Your browser does not support the video tag.
                        </video>
                    <?php else: ?>
                        <div class="d-flex align-items-center justify-content-center h-100 text-white">
                            <p>الفيديو غير متاح حالياً</p>
                        </div>
                    <?php endif; ?>
                <?php else: ?>
                    <div class="d-flex flex-column align-items-center justify-content-center h-100 text-white p-4">
                        <h3 class="mb-3">الفيديو متاح فقط للطلاب المسجلين في الدورة</h3>
                        <div>
                            <?php if(!auth()->check()): ?>
                                <a href="<?php echo e(route('login')); ?>?redirect=<?php echo e(urlencode(url()->full())); ?>" class="btn btn-primary me-2">تسجيل / تسجيل الدخول</a>
                            <?php else: ?>
                                <?php if($lesson->course->price > 0): ?>
                                    <a href="<?php echo e(route('payment.form', $lesson->course)); ?>" class="btn btn-success me-2">اشترك في الدورة</a>
                                <?php else: ?>
                                    <form action="<?php echo e(route('courses.attach', $lesson->course) ?? '#'); ?>" method="POST" style="display:inline">
                                        <?php echo csrf_field(); ?>
                                        <button class="btn btn-success me-2">انضم إلى الدورة</button>
                                    </form>
                                <?php endif; ?>
                            <?php endif; ?>
                            <a href="<?php echo e(env('PLATFORM_WHATSAPP') ? env('PLATFORM_WHATSAPP') : 'mailto:' . 'hemmah.platform.app@gmail.com'); ?>" target="_blank" class="btn btn-outline-light">طلب مساعدة</a>
                        </div>
                    </div>
                <?php endif; ?>
            </div>

            <div class="mt-4 bg-white p-4 rounded shadow-sm">
                <h1 class="h5 mb-2"><?php echo e($lesson->title); ?></h1>
                <p class="text-muted"><?php echo e($lesson->description); ?></p>
            </div>
        </div>

        <!-- Sidebar: lessons list -->
        <div class="col-12 col-lg-4">
            <div class="card shadow-sm" style="position:sticky; top:24px;">
                <div class="card-header bg-white">
                    <h6 class="mb-0">محتوى الدورة</h6>
                </div>
                <div class="list-group list-group-flush" style="max-height:70vh; overflow-y:auto;">
                    <?php $__currentLoopData = $lesson->course->lessons; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $l): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <a href="<?php echo e(route('lessons.show', $l)); ?>" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center <?php echo e($l->id === $lesson->id ? 'active' : ''); ?>">
                            <div class="d-flex align-items-center">
                                <div class="me-3">
                                    <?php if($l->id === $lesson->id): ?>
                                        <i class="fas fa-play-circle text-white"></i>
                                    <?php else: ?>
                                        <i class="far fa-play-circle text-muted"></i>
                                    <?php endif; ?>
                                </div>
                                <div>
                                    <div class="fw-medium"><?php echo e($l->title); ?></div>
                                    <div class="small text-muted"><?php echo e(Str::limit($l->description ?? '', 60)); ?></div>
                                </div>
                            </div>
                            <small class="text-muted"><?php echo e($l->duration); ?> دقيقة</small>
                        </a>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\himm23\resources\views/lessons/show.blade.php ENDPATH**/ ?>