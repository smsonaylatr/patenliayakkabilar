<?php if (isset($component)) { $__componentOriginal5863877a5171c196453bfa0bd807e410 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal5863877a5171c196453bfa0bd807e410 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.layouts.app','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('layouts.app'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

     <?php $__env->slot('title', null, []); ?> <?php echo e($post->meta_title ?? $post->title . ' | Patenli Ayakkabılar'); ?> <?php $__env->endSlot(); ?>
     <?php $__env->slot('description', null, []); ?> <?php echo e($post->meta_description ?? Str::limit(strip_tags($post->excerpt ?? $post->content), 155)); ?> <?php $__env->endSlot(); ?>
     <?php $__env->slot('ogType', null, []); ?> article <?php $__env->endSlot(); ?>
     <?php $__env->slot('canonical', null, []); ?> <?php echo e(url('/blog/' . $post->slug)); ?> <?php $__env->endSlot(); ?>
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($post->image_path): ?>
         <?php $__env->slot('ogImage', null, []); ?> <?php echo e(Storage::disk('public')->url($post->image_path)); ?> <?php $__env->endSlot(); ?>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(isset($post->is_indexable) && !$post->is_indexable): ?>
         <?php $__env->slot('robots', null, []); ?> noindex, follow <?php $__env->endSlot(); ?>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
     <?php $__env->slot('schema', null, []); ?> 
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(app()->bound(\App\Services\SchemaService::class)): ?>
            <?php echo app(\App\Services\SchemaService::class)->blogArticle($post); ?>

        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
     <?php $__env->endSlot(); ?>

    <style>
        .blog-content { font-size: 17px; line-height: 1.8; color: #374151; text-align: justify; }
        .blog-content h1 { font-size: 2rem; font-weight: 800; color: #111827; margin: 2.5rem 0 1rem; line-height: 1.3; }
        .blog-content h2 { font-size: 1.6rem; font-weight: 800; color: #111827; margin: 2.5rem 0 0.8rem; padding-bottom: 0.6rem; border-bottom: 2px solid #f3f4f6; line-height: 1.3; }
        .blog-content h3 { font-size: 1.3rem; font-weight: 700; color: #1f2937; margin: 2rem 0 0.6rem; line-height: 1.4; }
        .blog-content h4 { font-size: 1.1rem; font-weight: 700; color: #1f2937; margin: 1.5rem 0 0.5rem; }
        .blog-content p { margin: 0 0 1.2rem; }
        .blog-content a { color: #2563eb; font-weight: 500; text-decoration: none; }
        .blog-content a:hover { text-decoration: underline; }
        .blog-content strong, .blog-content b { color: #111827; font-weight: 700; }
        .blog-content ul, .blog-content ol { margin: 1rem 0 1.5rem 1.5rem; }
        .blog-content ul { list-style-type: disc; }
        .blog-content ol { list-style-type: decimal; }
        .blog-content li { margin-bottom: 0.4rem; line-height: 1.7; }
        .blog-content blockquote { margin: 1.5rem 0; padding: 1rem 1.5rem; border-left: 4px solid #3b82f6; background: #eff6ff; border-radius: 0 12px 12px 0; color: #1e40af; font-style: normal; }
        .blog-content img { max-width: 100%; height: auto; border-radius: 16px; margin: 1.5rem 0; box-shadow: 0 4px 16px rgba(0,0,0,0.08); }
        .blog-content code { background: #f3f4f6; padding: 2px 6px; border-radius: 4px; font-size: 0.9em; font-family: monospace; }
        .blog-content pre { background: #1f2937; color: #e5e7eb; padding: 1.2rem; border-radius: 12px; overflow-x: auto; margin: 1.5rem 0; }
        .blog-content pre code { background: none; padding: 0; color: inherit; }
        .blog-content table { width: 100%; border-collapse: collapse; margin: 1.5rem 0; }
        .blog-content th, .blog-content td { padding: 0.75rem 1rem; border: 1px solid #e5e7eb; text-align: left; }
        .blog-content th { background: #f9fafb; font-weight: 700; color: #111827; }
        .blog-content hr { border: none; border-top: 2px solid #f3f4f6; margin: 2rem 0; }
    </style>

    
    
    <div class="relative w-full overflow-hidden" style="min-height:280px;background:#111827;">
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($post->image_path): ?>
            <img src="<?php echo e(Storage::disk('public')->url($post->image_path)); ?>" 
                 alt="<?php echo e($post->title); ?>" 
                 class="w-full object-cover object-[80%_center] md:object-center"
                 style="max-height:500px;opacity:0.55;position:absolute;inset:0;width:100%;height:100%;"
                 fetchpriority="high">
        <?php else: ?>
            
            <div style="position:absolute;inset:0;background:linear-gradient(135deg, #0f172a 0%, #1e3a5f 40%, #0d9488 100%);"></div>
            <div style="position:absolute;inset:0;opacity:0.08;background-image:radial-gradient(circle, rgba(255,255,255,0.8) 1px, transparent 1px);background-size:30px 30px;"></div>
            <div style="position:absolute;top:50%;right:-60px;width:300px;height:300px;border-radius:50%;border:1px solid rgba(255,255,255,0.08);transform:translateY(-50%);"></div>
            <div style="position:absolute;top:50%;right:40px;width:180px;height:180px;border-radius:50%;border:1px solid rgba(255,255,255,0.06);transform:translateY(-50%);"></div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

        <div style="position:absolute;inset:0;background:linear-gradient(to top, rgba(17,24,39,0.85) 0%, rgba(17,24,39,0.2) 60%, rgba(17,24,39,0) 100%);"></div>

        <div style="position:relative;z-index:10;max-width:1100px;margin:0 auto;padding:5rem 1.5rem 3rem;display:flex;flex-direction:column;justify-content:flex-end;min-height:280px;">
            
            <div style="margin-bottom:1rem;">
                <span style="display:inline-block;padding:4px 14px;background:rgba(255,255,255,0.12);border:1px solid rgba(255,255,255,0.15);border-radius:999px;color:rgba(255,255,255,0.8);font-size:12px;font-weight:600;letter-spacing:0.5px;text-transform:uppercase;">
                    📝 Rehber
                </span>
            </div>

            <h1 style="font-size:2.4rem;font-weight:800;color:#fff;line-height:1.2;margin:0 0 1rem;max-width:800px;">
                <?php echo e($post->title); ?>

            </h1>

            <div style="display:flex;align-items:center;gap:14px;flex-wrap:wrap;">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($post->author_name): ?>
                    <div style="display:flex;align-items:center;gap:8px;">
                        <div style="width:32px;height:32px;background:linear-gradient(135deg,#3b82f6,#2563eb);border-radius:50%;display:flex;align-items:center;justify-content:center;color:#fff;font-weight:700;font-size:13px;">
                            <?php echo e(mb_substr($post->author_name, 0, 1)); ?>

                        </div>
                        <span style="color:rgba(255,255,255,0.85);font-size:14px;font-weight:500;"><?php echo e($post->author_name); ?></span>
                    </div>
                    <span style="width:4px;height:4px;background:rgba(255,255,255,0.3);border-radius:50%;display:inline-block;"></span>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                <time datetime="<?php echo e(($post->published_at ?? $post->created_at)->toW3cString()); ?>" 
                      style="color:rgba(255,255,255,0.6);font-size:14px;">
                    <?php echo e(($post->published_at ?? $post->created_at)->translatedFormat('d F Y')); ?>

                </time>
            </div>
        </div>
    </div>

    <div style="background:#fff;min-height:60vh;">
        <div style="max-width:1100px;margin:0 auto;padding:2.5rem 1.5rem 4rem;">

            
            <div style="margin-bottom:2rem;">
                <?php if (isset($component)) { $__componentOriginale19f62b34dfe0bfdf95075badcb45bc2 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginale19f62b34dfe0bfdf95075badcb45bc2 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.breadcrumb','data' => ['items' => [
                    ['name' => 'Ana Sayfa', 'url' => route('home')],
                    ['name' => 'Rehber Merkezi', 'url' => route('blog.index')],
                    ['name' => $post->title],
                ]]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('breadcrumb'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['items' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute([
                    ['name' => 'Ana Sayfa', 'url' => route('home')],
                    ['name' => 'Rehber Merkezi', 'url' => route('blog.index')],
                    ['name' => $post->title],
                ])]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginale19f62b34dfe0bfdf95075badcb45bc2)): ?>
<?php $attributes = $__attributesOriginale19f62b34dfe0bfdf95075badcb45bc2; ?>
<?php unset($__attributesOriginale19f62b34dfe0bfdf95075badcb45bc2); ?>
<?php endif; ?>
<?php if (isset($__componentOriginale19f62b34dfe0bfdf95075badcb45bc2)): ?>
<?php $component = $__componentOriginale19f62b34dfe0bfdf95075badcb45bc2; ?>
<?php unset($__componentOriginale19f62b34dfe0bfdf95075badcb45bc2); ?>
<?php endif; ?>
            </div>

            
            <article class="blog-content">
                <?php
                    $content = $post->content;
                    // İçerikteki ilk h1 veya h2'yi kaldır (hero'da zaten gösteriliyor)
                    $content = preg_replace('/^\s*<h[12][^>]*>.*?<\/h[12]>\s*/is', '', $content, 1);
                ?>
                <?php echo $content; ?>

            </article>

            
            <div style="margin-top:3.5rem;padding-top:2rem;border-top:2px solid #f3f4f6;">
                
                
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($post->author_name): ?>
                    <div style="display:flex;align-items:center;gap:14px;margin-bottom:2rem;">
                        <div style="width:48px;height:48px;background:linear-gradient(135deg,#1f2937,#4b5563);border-radius:50%;display:flex;align-items:center;justify-content:center;color:#fff;font-weight:700;font-size:18px;">
                            <?php echo e(mb_substr($post->author_name, 0, 1)); ?>

                        </div>
                        <div>
                            <p style="font-weight:700;color:#111827;margin:0;"><?php echo e($post->author_name); ?></p>
                            <p style="font-size:14px;color:#6b7280;margin:4px 0 0;">
                                <?php echo e(($post->published_at ?? $post->created_at)->translatedFormat('d F Y')); ?> tarihinde yayınlandı
                            </p>
                        </div>
                    </div>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                
                <style>
                    .minimal-cta-container {
                        background: #ffffff;
                        border-radius: 8px; 
                        border: 1px solid #e2e8f0;
                        display: flex; align-items: center; justify-content: space-between;
                        padding: 1.25rem 1.5rem; margin: 2rem 0;
                    }
                    .minimal-cta-content {
                        flex: 1; padding-right: 2rem;
                    }
                    .minimal-cta-title {
                        font-size: 1.15rem; font-weight: 700; color: #0f172a; margin: 0 0 0.35rem; line-height: 1.3;
                    }
                    .minimal-cta-text {
                        color: #64748b; font-size: 0.95rem; margin: 0; line-height: 1.5;
                    }
                    .minimal-cta-btn {
                        flex-shrink: 0; display: inline-flex; align-items: center; gap: 6px; 
                        padding: 10px 20px; background: #2F80ED; color: #fff !important; 
                        font-weight: 600; font-size: 0.9rem; border-radius: 6px; 
                        text-decoration: none !important; transition: opacity 0.2s ease;
                    }
                    .minimal-cta-btn:hover {
                        opacity: 0.9;
                    }
                    
                    @media (max-width: 640px) {
                        .minimal-cta-container {
                            flex-direction: column; align-items: flex-start; text-align: left; padding: 1.25rem;
                        }
                        .minimal-cta-content {
                            padding-right: 0; margin-bottom: 1rem;
                        }
                        .minimal-cta-btn {
                            width: 100%; justify-content: center;
                        }
                    }
                </style>
                <div class="minimal-cta-container">
                    <div class="minimal-cta-content">
                        <h3 class="minimal-cta-title">Patenli Ayakkabı Modellerini Keşfedin</h3>
                        <p class="minimal-cta-text">Işıklı ve tekerlekli en popüler modellerimize şimdi göz atın.</p>
                    </div>
                    <a href="<?php echo e(route('products.index')); ?>" wire:navigate class="minimal-cta-btn">
                        Ürünleri İncele
                        <i class="fa-solid fa-arrow-right" style="font-size: 12px;"></i>
                    </a>
                </div>
            </div>

            
            <?php
                $relatedPosts = \App\Models\BlogPost::where('status', true)
                    ->where('id', '!=', $post->id)
                    ->inRandomOrder()
                    ->take(4)
                    ->get();
            ?>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($relatedPosts->count()): ?>
                <div style="margin-top:3.5rem;">
                    <h2 style="font-size:1.5rem;font-weight:800;color:#111827;margin:0 0 1.5rem;">Diğer Yazılar</h2>
                    <div style="display:grid;grid-template-columns:repeat(auto-fill, minmax(200px, 1fr));gap:1.2rem;">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $relatedPosts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $related): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                            <a href="<?php echo e(url('/blog/' . $related->slug)); ?>" wire:navigate 
                               style="display:block;background:#f9fafb;border-radius:16px;overflow:hidden;text-decoration:none;transition:box-shadow 0.3s;">
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($related->image_path): ?>
                                    <div style="aspect-ratio:16/9;overflow:hidden;">
                                        <img src="<?php echo e(Storage::disk('public')->url($related->image_path)); ?>" 
                                             alt="<?php echo e($related->title); ?>"
                                             style="width:100%;height:100%;object-fit:cover;"
                                             loading="lazy">
                                    </div>
                                <?php else: ?>
                                    <div style="aspect-ratio:16/9;background:linear-gradient(135deg,#e5e7eb,#d1d5db);display:flex;align-items:center;justify-content:center;">
                                        <svg width="32" height="32" fill="none" stroke="#9ca3af" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/></svg>
                                    </div>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                <div style="padding:12px 14px;">
                                    <p style="font-size:12px;color:#6b7280;margin:0 0 4px;"><?php echo e(($related->published_at ?? $related->created_at)->translatedFormat('d M Y')); ?></p>
                                    <h3 style="font-weight:700;color:#111827;font-size:14px;line-height:1.4;margin:0;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;">
                                        <?php echo e($related->title); ?>

                                    </h3>
                                </div>
                            </a>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                    </div>
                </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
    </div>
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal5863877a5171c196453bfa0bd807e410)): ?>
<?php $attributes = $__attributesOriginal5863877a5171c196453bfa0bd807e410; ?>
<?php unset($__attributesOriginal5863877a5171c196453bfa0bd807e410); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal5863877a5171c196453bfa0bd807e410)): ?>
<?php $component = $__componentOriginal5863877a5171c196453bfa0bd807e410; ?>
<?php unset($__componentOriginal5863877a5171c196453bfa0bd807e410); ?>
<?php endif; ?>
<?php /**PATH C:\Users\Lenovo\Desktop\Projelerim\patenliayakkabilar.com\resources\views\blog\show.blade.php ENDPATH**/ ?>