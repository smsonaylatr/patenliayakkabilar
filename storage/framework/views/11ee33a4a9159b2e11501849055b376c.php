<?php
$settings = \Illuminate\Support\Facades\Cache::remember('hero_settings', 86400, function () {
    return \App\Models\Setting::whereIn('key', [
        'banner_pill_text', 'banner_title_1', 'banner_title_2', 'banner_title_3',
        'banner_desc', 'banner_btn1_text', 'banner_btn1_link', 'banner_btn2_text', 'banner_btn2_link',
        'banner_image_1', 'banner_image_2', 'banner_image_3', 'banner_bg_color_1', 'banner_bg_color_2',
    ])->pluck('value', 'key')->toArray();
});

$bgColor1 = $settings['banner_bg_color_1'] ?? '#ffffff';
$bgColor2 = $settings['banner_bg_color_2'] ?? '#f8fafc';

$pillText = $settings['banner_pill_text'] ?? '2026 · Yeni Koleksiyon';
$title1 = $settings['banner_title_1'] ?? 'Her Adımda';
$title2 = $settings['banner_title_2'] ?? 'Premium';
$title3 = $settings['banner_title_3'] ?? 'Bir Deneyim';
$desc = $settings['banner_desc'] ?? "500'den fazla model, sınırsız kombinasyon.\nLarcivert ile tarzını keşfet.";
$btn1Text = $settings['banner_btn1_text'] ?? 'Koleksiyonu Keşfet';
$btn1Link = $settings['banner_btn1_link'] ?? route('products.index');
$btn2Text = $settings['banner_btn2_text'] ?? 'İndirimleri Gör';
$btn2Link = $settings['banner_btn2_link'] ?? route('products.index') . '?indirim=true';

$img1 = $settings['banner_image_1'] ?? null;
$img2 = $settings['banner_image_2'] ?? null;
$img3 = $settings['banner_image_3'] ?? null;

$heroProducts = \Illuminate\Support\Facades\Cache::remember('hero_random_products', 3600, function () {
    return \App\Models\Product::with('images')->where('status', true)->inRandomOrder()->take(3)->get();
});

$mainImgUrl = $img1 ? \Illuminate\Support\Facades\Storage::disk('public')->url($img1) : ($heroProducts->get(0) && $heroProducts->get(0)->images->first() ? $heroProducts->get(0)->images->first()->image_url : 'https://images.unsplash.com/photo-1542291026-7eec264c27ff?w=700&q=90');
$subImgUrl1 = $img2 ? \Illuminate\Support\Facades\Storage::disk('public')->url($img2) : ($heroProducts->get(1) && $heroProducts->get(1)->images->first() ? $heroProducts->get(1)->images->first()->image_url : 'https://images.unsplash.com/photo-1606107557195-0e29a4b5b4aa?w=400&q=80');
$subImgUrl2 = $img3 ? \Illuminate\Support\Facades\Storage::disk('public')->url($img3) : ($heroProducts->get(2) && $heroProducts->get(2)->images->first() ? $heroProducts->get(2)->images->first()->image_url : 'https://images.unsplash.com/photo-1560769629-975ec94e6a86?w=400&q=80');
?>
<style>
    /* ========================
       3D HERO CSS (from Larcivert)
       ======================== */
    .hero {
      position: relative;
      display: flex;
      align-items: center;
      overflow: hidden;
      background: linear-gradient(180deg, <?php echo e($bgColor1); ?> 0%, <?php echo e($bgColor2); ?> 100%);
      font-family: 'Inter', sans-serif;
      padding-top: 20px;
      padding-bottom: 30px;
    }

    /* Background layers */
    .hero-bg {
      position: absolute;
      inset: 0;
      pointer-events: none;
    }

    .hero-noise {
      position: absolute;
      inset: 0;
      background-image: url("data:image/svg+xml,%3Csvg viewBox='0 0 200 200' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='n'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.9' numOctaves='4' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23n)' opacity='1'/%3E%3C/svg%3E");
      opacity: 0.025;
      mix-blend-mode: overlay;
    }

    .hero-grid {
      position: absolute;
      inset: 0;
      background-image:
        linear-gradient(rgba(15, 23, 42, 0.04) 1px, transparent 1px),
        linear-gradient(90deg, rgba(15, 23, 42, 0.04) 1px, transparent 1px);
      background-size: 60px 60px;
      transform: perspective(600px) rotateX(20deg) translateY(20%);
      transform-origin: bottom center;
      mask-image: linear-gradient(to top, rgba(0,0,0,0.2) 0%, transparent 60%);
      -webkit-mask-image: linear-gradient(to top, rgba(0,0,0,0.2) 0%, transparent 60%);
    }

    .hero-spotlight {
      position: absolute;
      top: -20%;
      left: -10%;
      width: 80vw;
      height: 80vw;
      max-width: 800px;
      max-height: 800px;
      background: radial-gradient(circle at center,
        rgba(255, 255, 255, 0.45) 0%,
        rgba(255, 255, 255, 0.15) 40%,
        transparent 70%
      );
      filter: blur(50px);
      border-radius: 50%;
      pointer-events: none;
      mix-blend-mode: overlay;
      animation: spotlight-wander 25s ease-in-out infinite;
      z-index: 1;
    }

    @keyframes spotlight-wander {
      0% { transform: translate(0, 0) scale(1); }
      25% { transform: translate(60vw, 20vh) scale(1.2); }
      50% { transform: translate(40vw, 60vh) scale(0.9); }
      75% { transform: translate(-10vw, 40vh) scale(1.1); }
      100% { transform: translate(0, 0) scale(1); }
    }

    .hero-orb {
      position: absolute;
      border-radius: 50%;
      filter: blur(120px);
      opacity: 0.18;
    }

    .orb-1 {
      width: 700px; height: 700px;
      background: radial-gradient(circle, rgba(96, 165, 250, 0.4), transparent);
      top: -30%; right: -5%;
      animation: float 10s ease-in-out infinite;
    }

    .orb-2 {
      width: 500px; height: 500px;
      background: radial-gradient(circle, rgba(167, 139, 250, 0.3), transparent);
      bottom: -20%; left: -5%;
      animation: float 14s ease-in-out infinite reverse;
    }

    .orb-3 {
      width: 350px; height: 350px;
      background: radial-gradient(circle, rgba(251, 113, 133, 0.3), transparent);
      top: 40%; right: 20%;
      animation: float 8s ease-in-out infinite 2s;
    }

    @keyframes float {
      0%, 100% { transform: translateY(0); }
      50% { transform: translateY(-12px); }
    }

    /* Main layout */
    .hero-inner {
      position: relative;
      z-index: 1;
      width: 100%;
      max-width: 1440px;
      margin: 0 auto;
      padding: 0 60px;
      display: grid;
      grid-template-columns: 1fr 1.1fr;
      align-items: center;
      gap: 40px;
    }

    /* LEFT: Text */
    .hero-text {
      will-change: transform;
      transition: transform 0.15s ease;
    }

    .hero-pill {
      display: inline-flex;
      align-items: center;
      gap: 8px;
      padding: 6px 16px;
      background: rgba(59, 130, 246, 0.1);
      border: 1px solid rgba(59, 130, 246, 0.2);
      border-radius: 999px;
      font-size: 0.8rem;
      font-weight: 600;
      color: #3b82f6;
      letter-spacing: 0.06em;
      margin-bottom: 28px;
    }

    .pill-dot {
      width: 6px; height: 6px;
      background: #3b82f6;
      border-radius: 50%;
      animation: pulse-glow 2s ease-in-out infinite;
    }

    .hero-title {
      display: flex;
      flex-direction: column;
      font-size: clamp(3rem, 5.5vw, 6rem);
      font-weight: 900;
      line-height: 0.95;
      letter-spacing: -0.04em;
      margin-bottom: 24px;
      color: #0f172a;
    }

    .title-line { display: block; }

    .title-gradient {
      background: linear-gradient(135deg, #60a5fa 0%, #a78bfa 50%, #60a5fa 100%);
      background-size: 200%;
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      background-clip: text;
      animation: shimmer 4s linear infinite;
    }

    @keyframes shimmer {
      0% { background-position: -200% center; }
      100% { background-position: 200% center; }
    }

    .hero-desc {
      font-size: 1.1rem;
      color: #475569;
      line-height: 1.75;
      margin-bottom: 36px;
      max-width: 420px;
    }

    .hero-actions {
      display: flex;
      align-items: center;
      gap: 12px;
      flex-wrap: wrap;
      margin-bottom: 40px;
    }

    .btn-hero-primary {
      display: inline-flex;
      align-items: center;
      gap: 10px;
      padding: 12px 28px;
      background: #0f172a;
      color: #ffffff;
      font-weight: 500;
      font-size: 0.85rem;
      letter-spacing: 0.1em;
      border-radius: 100px;
      border: 1px solid #0f172a;
      text-decoration: none;
      text-transform: uppercase;
      transition: all 0.4s ease;
      backdrop-filter: blur(10px);
    }

    .btn-hero-primary:hover {
      background: #1e293b;
      color: #ffffff;
      border-color: #1e293b;
      box-shadow: 0 5px 25px rgba(15, 23, 42, 0.2);
      transform: translateY(-2px);
    }

    .btn-hero-ghost {
      display: inline-flex;
      align-items: center;
      gap: 8px;
      padding: 12px 20px;
      background: transparent;
      border: 1px solid rgba(15, 23, 42, 0.2);
      color: #0f172a;
      font-weight: 500;
      font-size: 0.85rem;
      letter-spacing: 0.08em;
      border-radius: 100px;
      text-decoration: none;
      text-transform: uppercase;
      transition: all 0.4s ease;
    }

    .btn-hero-ghost:hover {
      color: #ffffff;
      background: #0f172a;
      transform: translateY(-2px);
    }

    .hero-stats {
      display: flex;
      align-items: center;
      gap: 28px;
    }

    .stat-item {
      display: flex;
      flex-direction: column;
      gap: 2px;
    }

    .stat-val {
      font-size: 1.5rem;
      font-weight: 900;
      color: #0f172a;
      letter-spacing: -0.02em;
    }

    .stat-lbl {
      font-size: 0.75rem;
      font-weight: 700;
      color: #334155;
      text-transform: uppercase;
      letter-spacing: 0.1em;
    }

    .stat-sep {
      width: 1px;
      height: 40px;
      background: rgba(15, 23, 42, 0.1);
    }

    /* RIGHT: 3D Stage */
    .hero-3d-stage {
      position: relative;
      height: 480px;
      will-change: transform;
      transition: transform 0.15s ease;
    }

    /* Main 3D card */
    .card-3d {
      position: absolute;
      border-radius: 20px;
      overflow: hidden;
      box-shadow:
        0 20px 50px rgba(15, 23, 42, 0.15),
        0 0 0 1px rgba(15, 23, 42, 0.05);
      will-change: transform;
      cursor: pointer;
    }

    .card-3d img {
      width: 100%;
      height: 100%;
      object-fit: contain;
      display: block;
      padding: 8px;
    }

    .card-3d-overlay {
      position: absolute;
      bottom: 0; left: 0; right: 0;
      padding: 20px;
      background: linear-gradient(to top, rgba(0,0,0,0.85) 0%, transparent 100%);
    }

    .card-3d-brand {
      font-size: 0.65rem;
      font-weight: 800;
      letter-spacing: 0.15em;
      color: #60a5fa;
      margin-bottom: 2px;
    }

    .card-3d-name {
      font-size: 0.95rem;
      font-weight: 700;
      color: white;
    }

    .card-3d-price {
      font-size: 0.9rem;
      font-weight: 600;
      color: rgba(255,255,255,0.7);
      margin-top: 2px;
    }

    .card-3d-shine {
      position: absolute;
      inset: 0;
      background: linear-gradient(
        135deg,
        rgba(255,255,255,0.4) 0%,
        transparent 50%,
        rgba(255,255,255,0.1) 100%
      );
      pointer-events: none;
    }

    .card-main {
      width: 380px;
      max-width: 380px;
      height: 300px;
      top: 65%;
      left: 70%;
      transform: perspective(1000px) translateX(-50%) translateY(-50%);
      animation: float-main 7s ease-in-out infinite;
      transition: transform 0.1s ease;
      background: #ffffff;
    }
    
    @keyframes float-main {
      0%, 100% { transform: perspective(1000px) translateX(-50%) translateY(-50%); }
      50% { transform: perspective(1000px) translateX(-50%) translateY(calc(-50% - 15px)); }
    }

    .card-sm {
      width: 200px;
      height: 150px;
      background: #ffffff;
    }

    .card-float-1 {
      top: -8%;
      right: 5%;
      animation: float 9s ease-in-out infinite reverse;
    }

    .card-float-2 {
      bottom: 15%;
      left: 5%;
      animation: float 11s ease-in-out infinite 1s;
    }

    /* Floating badges */
    .badge-float {
      position: absolute;
      border-radius: 999px;
      font-size: 0.78rem;
      font-weight: 700;
      padding: 8px 16px;
      backdrop-filter: blur(12px);
      box-shadow: 0 10px 25px rgba(15, 23, 42, 0.1);
      z-index: 10;
      white-space: nowrap;
    }

    .badge-rating {
      top: 30%;
      left: 15%;
      background: rgba(15, 23, 42, 0.6);
      color: #ffffff;
      border: 1px solid rgba(255, 255, 255, 0.1);
      animation: float 6s ease-in-out infinite 0.5s;
    }

    .badge-new {
      bottom: 30%;
      right: 2%;
      background: linear-gradient(135deg, #60a5fa 0%, #a78bfa 100%);
      border: none;
      color: #ffffff;
      animation: float 8s ease-in-out infinite reverse 1s;
    }

    .stage-glow {
      position: absolute;
      bottom: 0;
      left: 50%;
      transform: translateX(-50%);
      width: 300px;
      height: 100px;
      background: radial-gradient(ellipse, rgba(59,130,246,0.25), transparent 70%);
      filter: blur(20px);
      pointer-events: none;
    }

    /* Bottom fade */
    .hero-fade-bottom {
      position: absolute;
      bottom: 0; left: 0; right: 0;
      height: 160px;
      background: linear-gradient(to bottom, transparent, #ffffff);
      pointer-events: none;
      z-index: 2;
    }

    /* Scroll hint */
    .hero-scroll {
      position: absolute;
      bottom: 32px;
      left: 60px;
      display: flex;
      flex-direction: column;
      align-items: center;
      gap: 8px;
      z-index: 3;
    }

    .scroll-line {
      width: 1px;
      height: 48px;
      background: linear-gradient(to bottom, rgba(15, 23, 42, 0.4), transparent);
      animation: float 2s ease-in-out infinite;
    }

    .hero-scroll span {
      font-size: 0.68rem;
      font-weight: 600;
      letter-spacing: 0.15em;
      text-transform: uppercase;
      color: #64748b;
      writing-mode: vertical-rl;
    }

    @media (max-width: 1024px) {
      .hero-inner {
        grid-template-columns: 1fr;
        text-align: center;
        gap: 20px;
        padding: 0 30px;
      }
      .hero-title {
        font-size: 3.5rem;
      }
      .hero-actions {
        justify-content: center;
      }
      .hero-stats {
        justify-content: center;
      }
      .hero-pill {
        margin: 0 auto 20px auto;
      }
      .hero-3d-stage {
        height: 380px;
        margin-top: 40px;
        margin-bottom: 20px;
      }
      .card-main {
        width: 280px; height: 240px;
      }
      .card-float-1 {
        width: 140px; height: 110px;
        top: -60px; right: 0px;
      }
      .card-float-2 {
        width: 150px; height: 130px;
        bottom: -60px; left: 0px;
      }
      .hero-scroll {
        display: none;
      }
      
      /* Mobile Performance Optimizations */
      .hero-noise,
      .hero-grid,
      .hero-orb {
        display: none;
      }
      .badge-rating, .badge-new {
        animation: none;
        backdrop-filter: none;
      }
      .hero-text {
        transition: none !important;
        transform: none !important;
      }
      .hero-3d-stage {
        transition: none !important;
        transform: none !important;
      }
    }
    
    @media (max-width: 640px) {
      .hero {
        padding-top: 10px;
        padding-bottom: 20px;
      }
      .hero-inner {
        padding: 0 16px;
        gap: 30px;
      }
      .hero-pill {
        margin-bottom: 16px;
      }
      .hero-title {
        font-size: 2.8rem;
        line-height: 1.1;
        margin-bottom: 16px;
      }
      .hero-desc {
        font-size: 1rem;
        margin-bottom: 24px;
      }
      .hero-actions {
        flex-direction: column;
        width: 100%;
        gap: 12px;
      }
      .btn-hero-primary, .btn-hero-ghost {
        width: 100%;
        justify-content: center;
      }
      .hero-stats {
        flex-direction: row;
        flex-wrap: wrap;
        gap: 15px;
        margin-top: 10px;
      }
      .hero-3d-stage {
        height: 300px;
        margin-top: 30px;
        margin-bottom: 20px;
      }
      .card-main {
        width: 250px; height: 210px;
      }
      .card-float-1 {
        display: block;
        width: 130px; height: 105px;
        top: -15px; right: 5px;
      }
      .card-float-2 {
        display: block;
        width: 140px; height: 115px;
        bottom: -15px; left: 5px;
      }
      .badge-rating {
        top: 5%;
        left: 0%;
      }
      .badge-new {
        bottom: 10%;
        right: 0%;
      }
    }
  </style>
<section class="hero" id="larcivert-hero"
    x-data="{ mx: 0, my: 0, isMobile: window.innerWidth < 1024 }"
    @resize.window="isMobile = window.innerWidth < 1024"
    @mousemove="
        if (isMobile) return;
        const rect = $el.getBoundingClientRect();
        mx = ((($event.clientX - rect.left) / rect.width) - 0.5);
        my = ((($event.clientY - rect.top) / rect.height) - 0.5);
    "
    @mouseleave="mx = 0; my = 0"
>
  <!-- Ambient background layers -->
  <div class="hero-bg">
    <div class="hero-noise"></div>
    <div class="hero-grid"></div>
    <div class="hero-spotlight"></div>
    <div class="hero-orb orb-1"></div>
    <div class="hero-orb orb-2"></div>
    <div class="hero-orb orb-3"></div>
  </div>

  <!-- Full-width content -->
  <div class="hero-inner">
    <!-- Left: Text block -->
    <div class="hero-text" id="hero-text-block"
      :style="`transform: translate(${mx * -8}px, ${my * -5}px); transition: transform ${mx === 0 ? '0.8s ease' : '0.15s ease'}`">
      <div class="hero-pill">
        <span class="pill-dot"></span>
        <?php echo e($pillText); ?>

      </div>

      <h1 class="hero-title">
        <span class="title-line"><?php echo e($title1); ?></span>
        <span class="title-line title-gradient"><?php echo e($title2); ?></span>
        <span class="title-line"><?php echo e($title3); ?></span>
      </h1>

      <p class="hero-desc">
        <?php echo nl2br(e($desc)); ?>

      </p>

      <div class="hero-actions">
        <a href="<?php echo e($btn1Link); ?>" class="btn-hero-primary" wire:navigate>
          <?php echo e($btn1Text); ?>

          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" width="16" height="16"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
        </a>
        <a href="<?php echo e($btn2Link); ?>" class="btn-hero-ghost" wire:navigate>
          <?php echo e($btn2Text); ?>

        </a>
      </div>

      <div class="hero-stats">
        <div class="stat-item">
          <span class="stat-val">50+</span>
          <span class="stat-lbl">Model</span>
        </div>
        <div class="stat-sep"></div>
        <div class="stat-item">
          <span class="stat-val">2K+</span>
          <span class="stat-lbl">Müşteri</span>
        </div>
        <div class="stat-sep"></div>
        <div class="stat-item">
          <span class="stat-val">4.9★</span>
          <span class="stat-lbl">Puan</span>
        </div>
      </div>
    </div>

    <!-- Right: 3D floating product cards -->
    <div class="hero-3d-stage" id="hero-3d-stage"
      :style="`transform: translate(${mx * 12}px, ${my * 8}px); transition: transform ${mx === 0 ? '0.8s ease' : '0.15s ease'}`">
<?php
$isVideo = function($url) {
    return preg_match('/\.(mp4|webm|mov|ogg)$/i', $url);
};
?>
      <!-- Main big card -->
      <div class="card-3d card-main" id="hero-main-card"
        :style="`transform: perspective(1000px) translateX(-50%) translateY(-50%) rotateY(${mx * -8}deg) rotateX(${my * 5}deg) translateZ(0); transition: transform ${mx === 0 ? '0.8s ease' : '0.1s ease'}; animation: ${mx === 0 ? 'float-main 7s ease-in-out infinite' : 'none'};`">
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($isVideo($mainImgUrl)): ?>
            <video src="<?php echo e($mainImgUrl); ?>" autoplay loop muted playsinline style="width:100%; height:100%; object-fit:cover;"></video>
        <?php else: ?>
            <img src="<?php echo e($mainImgUrl); ?>" alt="Ana Kampanya Görseli" />
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
      </div>

      <!-- Floating card top-right -->
      <div class="card-3d card-sm card-float-1">
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($isVideo($subImgUrl1)): ?>
            <video src="<?php echo e($subImgUrl1); ?>" autoplay loop muted playsinline style="width:100%; height:100%; object-fit:cover;"></video>
        <?php else: ?>
            <img src="<?php echo e($subImgUrl1); ?>" alt="Kampanya Görseli 2" />
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
      </div>

      <!-- Floating card bottom-left -->
      <div class="card-3d card-sm card-float-2">
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($isVideo($subImgUrl2)): ?>
            <video src="<?php echo e($subImgUrl2); ?>" autoplay loop muted playsinline style="width:100%; height:100%; object-fit:cover;"></video>
        <?php else: ?>
            <img src="<?php echo e($subImgUrl2); ?>" alt="Kampanya Görseli 3" />
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
      </div>

      <!-- Rating badge floating -->
      <div class="badge-float badge-rating">⭐ 4.9 / 5.0</div>

      <!-- Ambient glow under cards -->
      <div class="stage-glow"></div>
    </div>
  </div>

  <!-- Bottom gradient fade -->
  <div class="hero-fade-bottom"></div>

  <!-- Scroll hint -->
  <div class="hero-scroll">
    <div class="scroll-line"></div>
    <span>Kaydır</span>
  </div>

  
</section>
<?php /**PATH C:\Users\Lenovo\Desktop\Projelerim\patenliayakkabilar.com\resources\views/livewire/home/hero-section.blade.php ENDPATH**/ ?>