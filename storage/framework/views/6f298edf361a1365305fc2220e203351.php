<div>
     <?php $__env->slot('title', null, []); ?> Rehber Merkezi | Patenli Ayakkabılar <?php $__env->endSlot(); ?>
     <?php $__env->slot('description', null, []); ?> Patenli ayakkabı rehberleri, kullanım kılavuzları, güvenlik ipuçları ve satın alma tavsiyeleri. Çocuğunuz için doğru patenli ayakkabıyı seçin. <?php $__env->endSlot(); ?>
     <?php $__env->slot('canonical', null, []); ?> <?php echo e(url('/blog')); ?> <?php $__env->endSlot(); ?>
     <?php $__env->slot('ogType', null, []); ?> website <?php $__env->endSlot(); ?>
     <?php $__env->slot('ogImage', null, []); ?> <?php echo e(asset('images/logo.png')); ?> <?php $__env->endSlot(); ?>
     <?php $__env->slot('schema', null, []); ?> 
        <script type="application/ld+json">
        {
            "@context": "https://schema.org",
            "@type": "Blog",
            "name": "Patenli Ayakkabılar Rehber Merkezi",
            "description": "Patenli ayakkabı rehberleri, kullanım kılavuzları, güvenlik ipuçları ve satın alma tavsiyeleri.",
            "url": "<?php echo e(url('/blog')); ?>"
        }
        </script>
     <?php $__env->endSlot(); ?>

    <div class="bg-gray-50 py-12 min-h-[60vh]">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            
            <div class="mb-8">
                <?php if (isset($component)) { $__componentOriginale19f62b34dfe0bfdf95075badcb45bc2 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginale19f62b34dfe0bfdf95075badcb45bc2 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.breadcrumb','data' => ['items' => [
                    ['name' => 'Ana Sayfa', 'url' => route('home')],
                    ['name' => 'Rehber Merkezi'],
                ]]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('breadcrumb'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['items' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute([
                    ['name' => 'Ana Sayfa', 'url' => route('home')],
                    ['name' => 'Rehber Merkezi'],
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

            <div class="text-center mb-12">
                <h1 class="text-4xl font-extrabold text-gray-900 tracking-tight sm:text-5xl">Rehber Merkezi</h1>
                <p class="mt-4 max-w-2xl text-xl text-gray-500 mx-auto">Patenli ayakkabı seçimi, kullanımı ve bakımı hakkında bilmeniz gereken her şey.</p>
                
                
                <div class="mt-8 max-w-xl mx-auto px-4 sm:px-0 relative" x-data="{ open: false }" @click.outside="open = false">
                    <div class="relative flex items-center">
                        <input 
                            type="text" 
                            wire:model.live.debounce.300ms="search"
                            @focus="open = true"
                            @input="open = true"
                            @keydown.escape="open = false"
                            placeholder="Blog yazılarında ara..." 
                            class="w-full pl-6 pr-24 py-3 border border-gray-300 rounded-full shadow-sm focus:ring-blue-500 focus:border-blue-500 text-gray-900 placeholder-gray-400"
                            autocomplete="off"
                        >
                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center gap-1">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(strlen($search) > 0): ?>
                                <button type="button" wire:click="clearSearch" aria-label="Aramayı Temizle" class="p-2 text-gray-400 hover:text-red-500 transition-colors">
                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                </button>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            <div class="p-2 text-gray-500">
                                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                            </div>
                        </div>
                    </div>

                    
                    <div x-show="open && $wire.search.length > 0" x-transition x-cloak class="absolute z-50 w-full mt-2 bg-white border border-gray-100 rounded-2xl shadow-lg overflow-hidden text-left" style="display: none;">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(strlen($search) > 0 && $suggestions->count() > 0): ?>
                            <ul class="py-2">
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $suggestions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $post): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                                    <li>
                                        <a href="<?php echo e(route('blog.show', $post->slug)); ?>" wire:navigate class="flex items-center px-6 py-3 text-sm text-gray-700 hover:bg-gray-50 hover:text-blue-600 transition-colors">
                                            <svg class="w-4 h-4 mr-3 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                                            <span class="truncate"><?php echo e($post->title); ?></span>
                                        </a>
                                    </li>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                            </ul>
                        <?php elseif(strlen($search) > 0): ?>
                            <div class="px-6 py-4 text-sm text-gray-500 text-center">
                                Başlıkta eşleşen sonuç bulunamadı.
                            </div>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                </div>
            </div>

            <div wire:loading.delay class="w-full text-center py-12">
                <p class="text-gray-500 font-medium text-lg">Yazılar aranıyor...</p>
            </div>

            <div wire:loading.remove.delay>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($posts->count() > 0): ?>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $posts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $post): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                            <article class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden flex flex-col hover:shadow-md transition-shadow duration-300 group">
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($post->image_path): ?>
                                    <a href="<?php echo e(route('blog.show', $post->slug)); ?>" wire:navigate tabindex="-1" aria-hidden="true" class="block aspect-[16/9] overflow-hidden">
                                        <img src="<?php echo e(asset('storage/' . $post->image_path)); ?>" 
                                             alt="<?php echo e($post->image_alt ?? $post->title); ?>" 
                                             class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
                                             loading="lazy">
                                    </a>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                <div class="p-6">
                                    <time class="text-xs font-medium text-gray-400 uppercase tracking-wider" datetime="<?php echo e($post->created_at->toW3cString()); ?>">
                                        <?php echo e($post->created_at->translatedFormat('d F Y')); ?>

                                    </time>
                                    <h2 class="mt-2 text-lg font-bold text-gray-900 group-hover:text-blue-600 transition-colors line-clamp-2">
                                        <a href="<?php echo e(route('blog.show', $post->slug)); ?>" wire:navigate><?php echo e($post->title); ?></a>
                                    </h2>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($post->excerpt): ?>
                                        <p class="mt-3 text-sm text-gray-500 line-clamp-3"><?php echo e($post->excerpt); ?></p>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    <a href="<?php echo e(route('blog.show', $post->slug)); ?>" wire:navigate tabindex="-1" aria-hidden="true" class="mt-4 inline-flex items-center text-sm font-semibold text-blue-600 hover:text-blue-800 transition-colors">
                                        Devamını Oku
                                        <svg class="ml-1 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                                    </a>
                                </div>
                            </article>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                    </div>

                    <div class="mt-12">
                        <?php echo e($posts->links()); ?>

                    </div>
                <?php else: ?>
                    <div class="text-center py-20">
                        <p class="text-xl font-medium text-gray-900">Sonuç bulunamadı.</p>
                        <p class="text-gray-500 mt-2">"<?php echo e($search); ?>" kelimesini içeren bir yazı henüz eklenmemiş.</p>
                        <button type="button" wire:click="clearSearch" class="mt-6 inline-flex items-center px-6 py-3 bg-blue-600 border border-transparent rounded-full font-semibold text-sm text-white tracking-wide hover:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 ring-blue-300 transition-all shadow-sm">
                            Tüm Yazıları Gör
                        </button>
                    </div>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>

            
            <div class="mt-16 bg-white rounded-2xl shadow-sm border border-gray-100 p-8">
                <h2 class="text-2xl font-bold text-gray-900 mb-4">Neden Patenli Ayakkabı Rehberini Okumalısınız?</h2>
                <div class="prose prose-blue max-w-none text-gray-600">
                    <p>Çocuklar ve gençler için son dönemin en popüler eğlence aracı olan <strong>patenli ayakkabılar</strong>, doğru seçildiğinde ve güvenli kullanıldığında harika bir fiziksel aktivitedir. Ancak ister <strong>ışıklı tekerlekli ayakkabı</strong> ister <strong>klasik çocuk patenli ayakkabı modelleri</strong> arıyor olun, doğru bedeni seçmek, tekerlek bakımını yapmak ve güvenlik ekipmanlarını doğru kullanmak hayati önem taşır.</p>
                    
                    <p class="mt-4">Rehber Merkezimizdeki içeriklerimiz, uzmanlar ve tecrübeli ebeveynlerin görüşleri doğrultusunda özel olarak hazırlanmaktadır. Blog yazılarımızda şu konuları bulabilirsiniz:</p>
                    <ul class="list-disc pl-5 mt-2 space-y-2">
                        <li>Patenli ayakkabı ile ilk sürüş teknikleri ve denge kurma pratikleri</li>
                        <li>Tekerlek bakımı, temizliği ve rulmanların uzun ömürlü kullanımı</li>
                        <li>Güvenli sürüş alanları ve dikkat edilmesi gereken risk faktörleri</li>
                        <li>Çocuğunuzun ayak gelişimini destekleyecek doğru ayakkabı seçimi (Ortopedik detaylar)</li>
                    </ul>
                    <p class="mt-4 text-sm text-gray-500">Düzenli olarak güncellenen blog içeriklerimizle hem sizin hem de çocuğunuzun daha güvenli ve keyifli bir sürüş deneyimi yaşamasını hedefliyoruz.</p>
                </div>
            </div>

        </div>
    </div>
</div>
<?php /**PATH C:\Users\Lenovo\Desktop\Projelerim\patenliayakkabilar.com\resources\views\livewire\frontend\blog-index.blade.php ENDPATH**/ ?>