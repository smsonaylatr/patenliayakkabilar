<?php if (isset($component)) { $__componentOriginal166a02a7c5ef5a9331faf66fa665c256 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal166a02a7c5ef5a9331faf66fa665c256 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'filament-panels::components.page.index','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('filament-panels::page'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

    <div class="grid grid-cols-1 gap-6">

        
        <div class="bg-gray-900 dark:bg-black rounded-3xl p-8 text-white shadow-xl flex flex-col md:flex-row items-center justify-between border border-gray-800">
            <div>
                <h2 class="text-3xl font-black tracking-tight mb-2 flex items-center gap-2">
                    <?php echo e(svg('heroicon-s-rocket-launch', 'w-8 h-8 text-blue-400')); ?>
                    YouTube Influencer Pazarlama Merkezi
                </h2>
                <p class="text-gray-400 max-w-xl text-sm leading-relaxed">
                    AI destekli influencer keşfi, otomatik teklif oluşturma ve kampanya performans takibi.
                    Patenli Ayakkabılar markasını çocuk YouTube kanallarıyla büyütün.
                </p>
            </div>
            <div class="mt-6 md:mt-0 text-center md:text-right">
                <div class="text-5xl font-black text-transparent bg-clip-text bg-gradient-to-r from-blue-400 to-emerald-400">
                    <?php echo e($stats['total_influencers'] ?? 0); ?>

                </div>
                <div class="text-xs font-bold tracking-widest text-gray-500 uppercase mt-1">Toplam Influencer</div>
            </div>
        </div>

        
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-4">
            
            <div class="group bg-white dark:bg-gray-900 rounded-2xl p-6 border border-gray-200 dark:border-gray-800 shadow-sm hover:shadow-md hover:border-blue-300 dark:hover:border-blue-700 transition-all duration-300">
                <div class="flex items-center justify-between mb-4">
                    <div class="p-2.5 rounded-xl bg-blue-50 dark:bg-blue-500/10">
                        <?php echo e(svg('heroicon-o-users', 'w-6 h-6 text-blue-600 dark:text-blue-400')); ?>
                    </div>
                    <span class="text-xs font-bold tracking-widest text-gray-400 uppercase">Influencer</span>
                </div>
                <div class="text-3xl font-black text-gray-900 dark:text-white">
                    <?php echo e($stats['total_influencers'] ?? 0); ?>

                </div>
                <div class="text-sm text-gray-500 mt-1">
                    <span class="text-emerald-600 dark:text-emerald-400 font-semibold"><?php echo e($stats['active_influencers'] ?? 0); ?></span> aktif ·
                    <span class="text-blue-600 dark:text-blue-400 font-semibold"><?php echo e($stats['contacted'] ?? 0); ?></span> iletişimde
                </div>
            </div>

            
            <div class="group bg-white dark:bg-gray-900 rounded-2xl p-6 border border-gray-200 dark:border-gray-800 shadow-sm hover:shadow-md hover:border-emerald-300 dark:hover:border-emerald-700 transition-all duration-300">
                <div class="flex items-center justify-between mb-4">
                    <div class="p-2.5 rounded-xl bg-emerald-50 dark:bg-emerald-500/10">
                        <?php echo e(svg('heroicon-o-rocket-launch', 'w-6 h-6 text-emerald-600 dark:text-emerald-400')); ?>
                    </div>
                    <span class="text-xs font-bold tracking-widest text-gray-400 uppercase">Kampanya</span>
                </div>
                <div class="text-3xl font-black text-gray-900 dark:text-white">
                    <?php echo e($stats['active_campaigns'] ?? 0); ?>

                </div>
                <div class="text-sm text-gray-500 mt-1">
                    <span class="text-gray-600 dark:text-gray-400 font-semibold"><?php echo e($stats['total_campaigns'] ?? 0); ?></span> toplam ·
                    <span class="text-emerald-600 dark:text-emerald-400 font-semibold"><?php echo e($stats['completed_campaigns'] ?? 0); ?></span> tamamlandı
                </div>
            </div>

            
            <div class="group bg-white dark:bg-gray-900 rounded-2xl p-6 border border-gray-200 dark:border-gray-800 shadow-sm hover:shadow-md hover:border-amber-300 dark:hover:border-amber-700 transition-all duration-300">
                <div class="flex items-center justify-between mb-4">
                    <div class="p-2.5 rounded-xl bg-amber-50 dark:bg-amber-500/10">
                        <?php echo e(svg('heroicon-o-currency-dollar', 'w-6 h-6 text-amber-600 dark:text-amber-400')); ?>
                    </div>
                    <span class="text-xs font-bold tracking-widest text-gray-400 uppercase">Gelir</span>
                </div>
                <div class="text-3xl font-black text-gray-900 dark:text-white">
                    <?php echo e(number_format($stats['total_revenue'] ?? 0, 2)); ?> ₺
                </div>
                <div class="text-sm text-gray-500 mt-1">
                    <span class="text-gray-600 dark:text-gray-400 font-semibold"><?php echo e(number_format($stats['total_views'] ?? 0)); ?></span> izlenme ·
                    <span class="text-gray-600 dark:text-gray-400 font-semibold"><?php echo e($stats['total_videos'] ?? 0); ?></span> video
                </div>
            </div>

            
            <div class="group bg-white dark:bg-gray-900 rounded-2xl p-6 border border-gray-200 dark:border-gray-800 shadow-sm hover:shadow-md hover:border-violet-300 dark:hover:border-violet-700 transition-all duration-300">
                <div class="flex items-center justify-between mb-4">
                    <div class="p-2.5 rounded-xl bg-violet-50 dark:bg-violet-500/10">
                        <?php echo e(svg('heroicon-o-chart-bar', 'w-6 h-6 text-violet-600 dark:text-violet-400')); ?>
                    </div>
                    <span class="text-xs font-bold tracking-widest text-gray-400 uppercase">ROI</span>
                </div>
                <div class="text-3xl font-black text-gray-900 dark:text-white">
                    %<?php echo e(number_format($stats['avg_roi'] ?? 0, 1)); ?>

                </div>
                <div class="text-sm text-gray-500 mt-1">
                    Ortalama yatırım getirisi
                </div>
            </div>
        </div>

        
        <div class="grid grid-cols-1 xl:grid-cols-2 gap-6">

            
            <div class="bg-white dark:bg-gray-900 rounded-2xl border border-gray-200 dark:border-gray-800 shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-800 flex items-center gap-2">
                    <?php echo e(svg('heroicon-o-clipboard-document-list', 'w-5 h-5 text-blue-600 dark:text-blue-400')); ?>
                    <h3 class="font-bold text-gray-900 dark:text-white text-lg">Görev Merkezi</h3>
                </div>
                <div class="p-6">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(count($tasks) > 0): ?>
                        <div class="space-y-4">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $tasks; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $task): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                                <?php
                                    $statusColor = match($task['status'] ?? 'pending') {
                                        'completed' => 'emerald',
                                        'in_progress' => 'blue',
                                        default => 'gray',
                                    };
                                    $statusLabel = match($task['status'] ?? 'pending') {
                                        'completed' => 'Tamamlandı',
                                        'in_progress' => 'Devam Ediyor',
                                        default => 'Bekliyor',
                                    };
                                    $progress = ($task['target'] ?? 0) > 0
                                        ? round(($task['current'] ?? 0) / $task['target'] * 100)
                                        : 0;
                                ?>
                                <div class="flex items-start gap-4 p-4 rounded-xl bg-gray-50 dark:bg-gray-800/50 hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors">
                                    <div class="flex-shrink-0 w-8 h-8 rounded-lg bg-<?php echo e($statusColor); ?>-100 dark:bg-<?php echo e($statusColor); ?>-500/20 flex items-center justify-center">
                                        <span class="text-sm font-black text-<?php echo e($statusColor); ?>-600 dark:text-<?php echo e($statusColor); ?>-400"><?php echo e($index + 1); ?></span>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-center justify-between mb-1">
                                            <h4 class="font-semibold text-gray-900 dark:text-white text-sm truncate"><?php echo e($task['name'] ?? 'Görev'); ?></h4>
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold tracking-wider uppercase
                                                bg-<?php echo e($statusColor); ?>-100 dark:bg-<?php echo e($statusColor); ?>-500/20
                                                text-<?php echo e($statusColor); ?>-700 dark:text-<?php echo e($statusColor); ?>-400">
                                                <?php echo e($statusLabel); ?>

                                            </span>
                                        </div>
                                        <div class="flex items-center gap-2 mt-2">
                                            <div class="flex-1 h-2 bg-gray-200 dark:bg-gray-700 rounded-full overflow-hidden">
                                                <div class="h-full bg-gradient-to-r from-blue-500 to-emerald-500 rounded-full transition-all duration-500"
                                                     style="width: <?php echo e($progress); ?>%"></div>
                                            </div>
                                            <span class="text-xs font-semibold text-gray-500 whitespace-nowrap">
                                                <?php echo e($task['current'] ?? 0); ?>/<?php echo e($task['target'] ?? 0); ?>

                                            </span>
                                        </div>
                                    </div>
                                </div>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-8">
                            <?php echo e(svg('heroicon-o-check-badge', 'w-12 h-12 mx-auto text-emerald-500 mb-3')); ?>
                            <p class="text-gray-500 dark:text-gray-400 text-sm">Tüm görevler tamamlandı! AI ile yeni görevler başlatın.</p>
                        </div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
            </div>

            
            <div class="bg-white dark:bg-gray-900 rounded-2xl border border-gray-200 dark:border-gray-800 shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-800 flex items-center gap-2">
                    <?php echo e(svg('heroicon-o-play-circle', 'w-5 h-5 text-emerald-600 dark:text-emerald-400')); ?>
                    <h3 class="font-bold text-gray-900 dark:text-white text-lg">Son Kampanyalar</h3>
                </div>
                <div class="p-6">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(count($recentCampaigns) > 0): ?>
                        <div class="space-y-3">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $recentCampaigns; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $campaign): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                                <?php
                                    $campStatusColor = match($campaign['status'] ?? 'draft') {
                                        'completed' => 'emerald',
                                        'in_progress', 'accepted' => 'blue',
                                        'sent' => 'amber',
                                        'rejected' => 'rose',
                                        default => 'gray',
                                    };
                                    $campStatusLabel = match($campaign['status'] ?? 'draft') {
                                        'completed' => 'Tamamlandı',
                                        'in_progress' => 'Devam Ediyor',
                                        'accepted' => 'Kabul Edildi',
                                        'sent' => 'Gönderildi',
                                        'rejected' => 'Reddedildi',
                                        'draft' => 'Taslak',
                                        default => $campaign['status'] ?? '-',
                                    };
                                ?>
                                <div class="flex items-center justify-between p-3 rounded-xl bg-gray-50 dark:bg-gray-800/50 hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors">
                                    <div class="flex items-center gap-3 min-w-0">
                                        <div class="flex-shrink-0 w-8 h-8 rounded-lg bg-gradient-to-br from-blue-500 to-emerald-500 flex items-center justify-center">
                                            <?php echo e(svg('heroicon-s-play', 'w-4 h-4 text-white')); ?>
                                        </div>
                                        <div class="min-w-0">
                                            <p class="text-sm font-semibold text-gray-900 dark:text-white truncate">
                                                <?php echo e($campaign['influencer']['channel_name'] ?? 'Bilinmeyen Kanal'); ?>

                                            </p>
                                            <p class="text-xs text-gray-500 truncate">
                                                <?php echo e($campaign['package_type'] ?? '-'); ?> · <?php echo e($campaign['created_at'] ? \Carbon\Carbon::parse($campaign['created_at'])->format('d.m.Y') : '-'); ?>

                                            </p>
                                        </div>
                                    </div>
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold tracking-wider uppercase flex-shrink-0
                                        bg-<?php echo e($campStatusColor); ?>-100 dark:bg-<?php echo e($campStatusColor); ?>-500/20
                                        text-<?php echo e($campStatusColor); ?>-700 dark:text-<?php echo e($campStatusColor); ?>-400">
                                        <?php echo e($campStatusLabel); ?>

                                    </span>
                                </div>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-8">
                            <?php echo e(svg('heroicon-o-video-camera', 'w-12 h-12 mx-auto text-gray-400 mb-3')); ?>
                            <p class="text-gray-500 dark:text-gray-400 text-sm">Henüz kampanya yok. AI ile teklif oluşturarak başlayın.</p>
                        </div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
            </div>
        </div>

        
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($lastAiResult): ?>
            <div class="bg-white dark:bg-gray-900 rounded-2xl border border-gray-200 dark:border-gray-800 shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-800 flex items-center gap-2">
                    <?php echo e(svg('heroicon-o-sparkles', 'w-5 h-5 text-amber-500')); ?>
                    <h3 class="font-bold text-gray-900 dark:text-white text-lg">AI Analiz Sonucu</h3>
                </div>
                <div class="p-6">
                    <pre class="bg-gray-950 text-emerald-400 rounded-xl p-6 text-sm font-mono overflow-x-auto leading-relaxed whitespace-pre-wrap"><?php echo e($lastAiResult); ?></pre>
                </div>
            </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    </div>
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal166a02a7c5ef5a9331faf66fa665c256)): ?>
<?php $attributes = $__attributesOriginal166a02a7c5ef5a9331faf66fa665c256; ?>
<?php unset($__attributesOriginal166a02a7c5ef5a9331faf66fa665c256); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal166a02a7c5ef5a9331faf66fa665c256)): ?>
<?php $component = $__componentOriginal166a02a7c5ef5a9331faf66fa665c256; ?>
<?php unset($__componentOriginal166a02a7c5ef5a9331faf66fa665c256); ?>
<?php endif; ?>
<?php /**PATH C:\Users\Lenovo\Desktop\Projelerim\patenliayakkabilar.com\resources\views\filament\pages\influencer-marketing.blade.php ENDPATH**/ ?>