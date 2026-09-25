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

    
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:24px;">

        
        <div style="background:#111827;border-radius:12px;padding:20px;border:1px solid rgba(255,255,255,0.08);">
            <h3 style="font-size:14px;font-weight:700;color:#fff;margin-bottom:16px;">📊 Stok Durumu</h3>
            <div style="display:flex;align-items:center;gap:20px;">
                
                <div style="position:relative;width:130px;height:130px;flex-shrink:0;" wire:ignore>
                    <canvas id="donutChart" width="130" height="130"></canvas>
                    <div style="position:absolute;top:50%;left:50%;transform:translate(-50%,-50%);text-align:center;">
                        <div style="font-size:22px;font-weight:800;color:#fff;"><?php echo e($totalStock); ?></div>
                        <div style="font-size:10px;color:#9ca3af;">Toplam Adet</div>
                    </div>
                </div>
                
                <div style="flex:1;">
                    <div style="display:flex;align-items:center;gap:8px;margin-bottom:10px;">
                        <span style="width:10px;height:10px;border-radius:50%;background:#22c55e;display:inline-block;"></span>
                        <span style="color:#9ca3af;font-size:12px;flex:1;">Stokta</span>
                        <span style="color:#fff;font-size:14px;font-weight:700;"><?php echo e($inStock); ?></span>
                    </div>
                    <div style="display:flex;align-items:center;gap:8px;margin-bottom:10px;">
                        <span style="width:10px;height:10px;border-radius:50%;background:#eab308;display:inline-block;"></span>
                        <span style="color:#9ca3af;font-size:12px;flex:1;">Düşük Stok</span>
                        <span style="color:#fff;font-size:14px;font-weight:700;"><?php echo e($lowStock); ?></span>
                    </div>
                    <div style="display:flex;align-items:center;gap:8px;margin-bottom:10px;">
                        <span style="width:10px;height:10px;border-radius:50%;background:#ef4444;display:inline-block;"></span>
                        <span style="color:#9ca3af;font-size:12px;flex:1;">Tükenen</span>
                        <span style="color:#fff;font-size:14px;font-weight:700;"><?php echo e($outOfStock); ?></span>
                    </div>
                    <div style="border-top:1px solid rgba(255,255,255,0.1);padding-top:10px;margin-top:4px;display:flex;align-items:center;gap:8px;">
                        <span style="width:10px;height:10px;border-radius:50%;background:#6366f1;display:inline-block;"></span>
                        <span style="color:#9ca3af;font-size:12px;flex:1;">Toplam Ürün</span>
                        <span style="color:#fff;font-size:14px;font-weight:700;"><?php echo e($totalProducts); ?></span>
                    </div>
                </div>
            </div>
        </div>

        
        <div style="background:#111827;border-radius:12px;padding:20px;border:1px solid rgba(255,255,255,0.08);">
            <h3 style="font-size:14px;font-weight:700;color:#fff;margin-bottom:16px;">📈 Son 14 Gün Stok Hareketleri</h3>
            <div style="position:relative;width:100%;height:140px;" wire:ignore>
                <canvas id="movementChart" style="width:100%;height:140px;"></canvas>
            </div>
        </div>
    </div>

    
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:24px;">
        
        
        <div style="background:#111827;border-radius:12px;padding:16px 20px;border:1px solid rgba(255,255,255,0.08);">
            <h3 style="font-size:13px;font-weight:600;color:#9ca3af;margin-bottom:10px;">📦 Beden Bazlı Stok Dağılımı</h3>
            <div style="display:flex;align-items:flex-end;gap:4px;height:60px;">
                <?php $maxSize = max(array_values($sizeDistribution) ?: [1]); ?>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $sizeDistribution; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sizeNum => $sizeTotal): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                    <?php
                        $pct = $maxSize > 0 ? ($sizeTotal / $maxSize * 100) : 0;
                        if ($sizeTotal <= 0) $barColor = '#ef4444';
                        elseif ($sizeTotal <= 10) $barColor = '#f97316';
                        elseif ($sizeTotal <= 20) $barColor = '#eab308';
                        else $barColor = '#22c55e';
                    ?>
                    <div style="flex:1;display:flex;flex-direction:column;align-items:center;gap:2px;">
                        <span style="font-size:9px;color:#fff;font-weight:600;"><?php echo e($sizeTotal); ?></span>
                        <div style="width:100%;height:<?php echo e(max($pct * 0.4, 2)); ?>px;background:<?php echo e($barColor); ?>;border-radius:3px 3px 0 0;"></div>
                        <span style="font-size:9px;color:#6b7280;"><?php echo e($sizeNum); ?></span>
                    </div>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
            </div>
        </div>

        
        <div style="background:#111827;border-radius:12px;padding:16px 20px;border:1px solid rgba(255,255,255,0.08);">
            <h3 style="font-size:13px;font-weight:600;color:#9ca3af;margin-bottom:10px;">🏆 En Çok Sipariş Edilen Bedenler</h3>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(empty($orderedSizes)): ?>
                <div style="display:flex;align-items:center;justify-content:center;height:60px;color:#6b7280;font-size:12px;">Henüz sipariş verisi yok.</div>
            <?php else: ?>
                <?php 
                    $maxOrdered = max(array_values($orderedSizes) ?: [1]);
                    $totalOrdered = array_sum($orderedSizes);
                    // En çok siparişe göre sırala
                    arsort($orderedSizes);
                    $topSizes = array_slice($orderedSizes, 0, 3, true);
                    // Tekrar beden sırasına dön
                    ksort($orderedSizes);
                ?>
                <div style="display:flex;align-items:flex-end;gap:4px;height:60px;">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $orderedSizes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sizeNum => $sizeOrdered): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                        <?php
                            $pct = $maxOrdered > 0 ? ($sizeOrdered / $maxOrdered * 100) : 0;
                            $isTop = array_key_exists($sizeNum, $topSizes);
                            $barColor = $isTop ? '#6366f1' : '#3b82f6';
                            $orderPct = $totalOrdered > 0 ? round($sizeOrdered / $totalOrdered * 100) : 0;
                        ?>
                        <div style="flex:1;display:flex;flex-direction:column;align-items:center;gap:2px;" title="<?php echo e($sizeNum); ?> numara: <?php echo e($sizeOrdered); ?> sipariş (%<?php echo e($orderPct); ?>)">
                            <span style="font-size:9px;color:<?php echo e($isTop ? '#a5b4fc' : '#93c5fd'); ?>;font-weight:<?php echo e($isTop ? '800' : '600'); ?>;"><?php echo e($sizeOrdered); ?></span>
                            <div style="width:100%;height:<?php echo e(max($pct * 0.4, 2)); ?>px;background:<?php echo e($barColor); ?>;border-radius:3px 3px 0 0;<?php echo e($isTop ? 'box-shadow:0 0 6px rgba(99,102,241,0.5);' : ''); ?>"></div>
                            <span style="font-size:9px;color:<?php echo e($isTop ? '#a5b4fc' : '#6b7280'); ?>;font-weight:<?php echo e($isTop ? '700' : '400'); ?>;"><?php echo e($sizeNum); ?></span>
                        </div>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                </div>
                
                <div style="display:flex;gap:12px;margin-top:8px;padding-top:6px;border-top:1px solid rgba(255,255,255,0.06);">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $topSizes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $topSize => $topCount): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                        <?php $topPct = $totalOrdered > 0 ? round($topCount / $totalOrdered * 100) : 0; ?>
                        <span style="font-size:10px;color:#a5b4fc;">🥇 <?php echo e($topSize); ?> No: <strong><?php echo e($topCount); ?></strong> adet (%<?php echo e($topPct); ?>)</span>
                        <?php break; ?>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
    </div>

    
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.7/dist/chart.umd.min.js"></script>
    <script>
    (function() {
        function init() {
            // Donut
            var dc = document.getElementById('donutChart');
            if (dc) {
                new Chart(dc.getContext('2d'), {
                    type: 'doughnut',
                    data: {
                        labels: ['Stokta', 'Düşük', 'Tükenen'],
                        datasets: [{
                            data: [<?php echo e($inStock - $lowStock); ?>, <?php echo e($lowStock); ?>, <?php echo e($outOfStock); ?>],
                            backgroundColor: ['#22c55e', '#eab308', '#ef4444'],
                            borderWidth: 0,
                            borderRadius: 3,
                        }]
                    },
                    options: {
                        responsive: false,
                        cutout: '70%',
                        plugins: { legend: { display: false } }
                    }
                });
            }

            // Bar
            var mc = document.getElementById('movementChart');
            if (mc) {
                new Chart(mc.getContext('2d'), {
                    type: 'bar',
                    data: {
                        labels: <?php echo json_encode($chartLabels, 15, 512) ?>,
                        datasets: [
                            { label: 'Giriş', data: <?php echo json_encode($chartIn, 15, 512) ?>, backgroundColor: 'rgba(34,197,94,0.7)', borderRadius: 3 },
                            { label: 'Çıkış', data: <?php echo json_encode($chartOut, 15, 512) ?>, backgroundColor: 'rgba(239,68,68,0.7)', borderRadius: 3 }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            y: { beginAtZero: true, ticks: { color: '#6b7280', stepSize: 1 }, grid: { color: 'rgba(255,255,255,0.05)' } },
                            x: { ticks: { color: '#6b7280', font: { size: 10 } }, grid: { display: false } }
                        },
                        plugins: {
                            legend: { position: 'top', labels: { color: '#9ca3af', boxWidth: 12, font: { size: 11 } } }
                        }
                    }
                });
            }
        }
        if (typeof Chart !== 'undefined') init();
        else document.addEventListener('DOMContentLoaded', function(){ setTimeout(init, 300); });
    })();
    </script>

    
    <?php if (isset($component)) { $__componentOriginalee08b1367eba38734199cf7829b1d1e9 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalee08b1367eba38734199cf7829b1d1e9 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'filament::components.section.index','data' => ['class' => 'mb-6']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('filament::section'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'mb-6']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

         <?php $__env->slot('heading', null, []); ?> 
            🗂️ Beden Stok Matrisi — <span class="text-xs font-normal text-gray-400">Hücreye tıklayarak stok düzenleyebilirsiniz</span>
         <?php $__env->endSlot(); ?>

        <div style="overflow-x:auto;">
            <table style="width:100%;border-collapse:collapse;table-layout:fixed;">
                <colgroup>
                    <col style="width:260px;">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $sizes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $size): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                        <col style="width:44px;">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                    <col style="width:56px;">
                </colgroup>
                <thead>
                    <tr style="border-bottom:2px solid #e5e7eb;">
                        <th style="padding:8px 6px;text-align:left;font-size:12px;font-weight:600;color:#6b7280;">Ürün</th>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $sizes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $size): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                            <th style="padding:8px 2px;text-align:center;font-size:11px;font-weight:700;color:#4b5563;"><?php echo e($size); ?></th>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                        <th style="padding:8px 4px;text-align:center;font-size:11px;font-weight:700;color:#374151;background:#f9fafb;">Top.</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $matrix; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $rowIndex => $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                        <tr style="border-bottom:1px solid rgba(255,255,255,0.05);">
                            
                            <td style="padding:4px 6px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;font-size:11px;font-weight:500;" title="<?php echo e($row['name']); ?>">
                                <div style="display:flex;align-items:center;gap:8px;">
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($row['image']): ?>
                                        <img src="<?php echo e(asset('storage/' . $row['image'])); ?>" style="width:40px;height:40px;border-radius:6px;object-fit:cover;flex-shrink:0;" loading="lazy">
                                    <?php else: ?>
                                        <span style="width:40px;height:40px;border-radius:6px;background:#374151;display:inline-flex;align-items:center;justify-content:center;flex-shrink:0;font-size:16px;">👟</span>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    <span class="text-gray-900 dark:text-white" style="overflow:hidden;text-overflow:ellipsis;white-space:nowrap;"><?php echo e($row['name']); ?></span>
                                </div>
                            </td>

                            
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $sizes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $size): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                                <?php
                                    $sizeData = $row['sizes'][$size];
                                    $stock = $sizeData['stock'];
                                    $variantId = $sizeData['variant_id'];
                                    
                                    if ($stock !== null) {
                                        if ($stock <= 0) { $bg = '#ef4444'; $fg = '#fff'; }
                                        elseif ($stock <= 3) { $bg = '#f97316'; $fg = '#fff'; }
                                        elseif ($stock <= 5) { $bg = '#eab308'; $fg = '#111'; }
                                        else { $bg = '#22c55e'; $fg = '#fff'; }
                                    } else {
                                        $bg = '#374151'; $fg = '#6b7280';
                                    }
                                ?>
                                <td style="padding:2px 1px;text-align:center;vertical-align:middle;">
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($stock !== null && $variantId): ?>
                                        <div x-data="{ editing: false, value: <?php echo e($stock); ?>, original: <?php echo e($stock); ?> }">
                                            <button
                                                x-show="!editing"
                                                x-cloak
                                                @click="editing = true; $nextTick(() => { let el = $refs['i<?php echo e($variantId); ?>']; if(el){el.focus();el.select();} })"
                                                style="width:38px;height:26px;border-radius:4px;font-size:11px;font-weight:700;cursor:pointer;border:none;display:inline-flex;align-items:center;justify-content:center;transition:transform 0.15s;background:<?php echo e($bg); ?>;color:<?php echo e($fg); ?>;"
                                                onmouseover="this.style.transform='scale(1.15)'"
                                                onmouseout="this.style.transform='scale(1)'"
                                            ><?php echo e($stock); ?></button>

                                            <input
                                                x-show="editing"
                                                x-cloak
                                                x-ref="i<?php echo e($variantId); ?>"
                                                type="number" min="0"
                                                x-model.number="value"
                                                @keydown.enter="if(value!==original&&value>=0){$wire.updateMatrixStock(<?php echo e($variantId); ?>,value);original=value}editing=false"
                                                @keydown.escape="value=original;editing=false"
                                                @click.outside="if(value!==original&&value>=0){$wire.updateMatrixStock(<?php echo e($variantId); ?>,value);original=value}editing=false"
                                                style="width:38px;height:26px;border-radius:4px;font-size:11px;font-weight:700;text-align:center;border:2px solid #6366f1;outline:none;-moz-appearance:textfield;"
                                            >
                                        </div>
                                    <?php else: ?>
                                        <span style="display:inline-flex;width:38px;height:26px;align-items:center;justify-content:center;border-radius:4px;background:#374151;color:#6b7280;font-size:11px;">—</span>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </td>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>

                            
                            <?php
                                if ($row['total'] <= 0) { $tbg = '#fecaca'; $tfg = '#b91c1c'; }
                                elseif ($row['total'] <= 10) { $tbg = '#fef3c7'; $tfg = '#92400e'; }
                                else { $tbg = '#d1fae5'; $tfg = '#065f46'; }
                            ?>
                            <td style="padding:2px 4px;text-align:center;vertical-align:middle;">
                                <span style="display:inline-flex;align-items:center;justify-content:center;height:22px;padding:0 8px;border-radius:12px;font-size:11px;font-weight:700;background:<?php echo e($tbg); ?>;color:<?php echo e($tfg); ?>;"><?php echo e($row['total']); ?></span>
                            </td>
                        </tr>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                </tbody>
            </table>

            
            <div style="display:flex;align-items:center;gap:16px;margin-top:12px;padding-top:12px;border-top:1px solid #f3f4f6;font-size:11px;color:#6b7280;">
                <span style="color:#9ca3af;">Renk:</span>
                <span style="display:flex;align-items:center;gap:4px;"><span style="width:12px;height:12px;border-radius:3px;background:#ef4444;display:inline-block;"></span> Tükendi</span>
                <span style="display:flex;align-items:center;gap:4px;"><span style="width:12px;height:12px;border-radius:3px;background:#f97316;display:inline-block;"></span> Kritik (1-3)</span>
                <span style="display:flex;align-items:center;gap:4px;"><span style="width:12px;height:12px;border-radius:3px;background:#eab308;display:inline-block;"></span> Düşük (4-5)</span>
                <span style="display:flex;align-items:center;gap:4px;"><span style="width:12px;height:12px;border-radius:3px;background:#22c55e;display:inline-block;"></span> Yeterli (6+)</span>
            </div>
        </div>
     <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalee08b1367eba38734199cf7829b1d1e9)): ?>
<?php $attributes = $__attributesOriginalee08b1367eba38734199cf7829b1d1e9; ?>
<?php unset($__attributesOriginalee08b1367eba38734199cf7829b1d1e9); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalee08b1367eba38734199cf7829b1d1e9)): ?>
<?php $component = $__componentOriginalee08b1367eba38734199cf7829b1d1e9; ?>
<?php unset($__componentOriginalee08b1367eba38734199cf7829b1d1e9); ?>
<?php endif; ?>

    
    <?php echo e($this->table); ?>


    
    <div class="mt-8">
        <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">📋 Stok Hareket Geçmişi</h3>
        <?php
$__split = function ($name, $params = []) {
    return [$name, $params];
};
[$__name, $__params] = $__split(\App\Livewire\Admin\StockMovementHistory::class);

$__keyOuter = $__key ?? null;

$__key = null;
$__componentSlots = [];

$__key ??= \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::generateKey('lw-446786730-0', $__key);

$__html = app('livewire')->mount($__name, $__params, $__key, $__componentSlots);

echo $__html;

unset($__html);
unset($__key);
$__key = $__keyOuter;
unset($__keyOuter);
unset($__name);
unset($__params);
unset($__componentSlots);
unset($__split);
?>
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
<?php /**PATH C:\Users\Lenovo\Desktop\Projelerim\patenliayakkabilar.com\resources\views\filament\pages\stock-management.blade.php ENDPATH**/ ?>