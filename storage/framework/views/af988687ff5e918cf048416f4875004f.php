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

    
    <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:16px;margin-bottom:24px;">
        
        <div style="background:#111827;border-radius:12px;padding:20px;border:1px solid rgba(255,255,255,0.08);">
            <div style="display:flex;align-items:center;gap:10px;margin-bottom:8px;">
                <span style="font-size:24px;">📦</span>
                <span style="font-size:12px;color:#9ca3af;font-weight:600;">Teslim Edilen</span>
            </div>
            <div style="font-size:28px;font-weight:800;color:#22c55e;"><?php echo e(number_format($totalDelivered)); ?></div>
            <div style="font-size:11px;color:#6b7280;margin-top:4px;">adet ürün</div>
        </div>

        
        <div style="background:#111827;border-radius:12px;padding:20px;border:1px solid rgba(255,255,255,0.08);">
            <div style="display:flex;align-items:center;gap:10px;margin-bottom:8px;">
                <span style="font-size:24px;">💰</span>
                <span style="font-size:12px;color:#9ca3af;font-weight:600;">Toplam Ciro</span>
            </div>
            <div style="font-size:28px;font-weight:800;color:#f59e0b;"><?php echo e(number_format($totalRevenue, 2)); ?> ₺</div>
            <div style="font-size:11px;color:#6b7280;margin-top:4px;"><?php echo e(number_format($uniqueCustomers)); ?> müşteri · <?php echo e(number_format($uniqueProducts)); ?> ürün çeşidi</div>
        </div>

        
        <div style="background:#111827;border-radius:12px;padding:20px;border:1px solid rgba(255,255,255,0.08);">
            <div style="display:flex;align-items:center;gap:10px;margin-bottom:8px;">
                <span style="font-size:24px;">🔄</span>
                <span style="font-size:12px;color:#9ca3af;font-weight:600;">İade Edilen</span>
            </div>
            <div style="font-size:28px;font-weight:800;color:#ef4444;"><?php echo e(number_format($totalReturned)); ?></div>
            <div style="font-size:11px;color:#6b7280;margin-top:4px;"><?php echo e(number_format($totalReturnedRevenue, 2)); ?> ₺ tutar</div>
        </div>

        
        <div style="background:#111827;border-radius:12px;padding:20px;border:1px solid rgba(255,255,255,0.08);">
            <div style="display:flex;align-items:center;gap:10px;margin-bottom:8px;">
                <span style="font-size:24px;">📊</span>
                <span style="font-size:12px;color:#9ca3af;font-weight:600;">İade Oranı</span>
            </div>
            <div style="font-size:28px;font-weight:800;color:<?php echo e($returnRate > 10 ? '#ef4444' : ($returnRate > 5 ? '#f59e0b' : '#22c55e')); ?>;">%<?php echo e($returnRate); ?></div>
            <div style="font-size:11px;color:#6b7280;margin-top:4px;"><?php echo e($returnRate <= 5 ? '✅ İyi oran' : ($returnRate <= 10 ? '⚠️ Dikkat' : '🚨 Yüksek')); ?></div>
        </div>
    </div>

    
    <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:16px;margin-bottom:24px;">

        
        <div style="background:#111827;border-radius:12px;padding:20px;border:1px solid rgba(255,255,255,0.08);">
            <h3 style="font-size:14px;font-weight:700;color:#fff;margin-bottom:16px;">🏆 En Çok Satılanlar</h3>
            <div style="max-height:350px;overflow-y:auto;">
                <table style="width:100%;border-collapse:collapse;">
                    <thead>
                        <tr style="border-bottom:1px solid rgba(255,255,255,0.1);">
                            <th style="text-align:left;padding:6px 4px;font-size:10px;color:#9ca3af;font-weight:600;">#</th>
                            <th style="text-align:left;padding:6px 4px;font-size:10px;color:#9ca3af;font-weight:600;">Ürün</th>
                            <th style="text-align:center;padding:6px 4px;font-size:10px;color:#9ca3af;font-weight:600;">Adet</th>
                            <th style="text-align:right;padding:6px 4px;font-size:10px;color:#9ca3af;font-weight:600;">Ciro</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $productSummary; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $ps): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                            <tr style="border-bottom:1px solid rgba(255,255,255,0.05);<?php echo e($index < 3 ? 'background:rgba(34,197,94,0.05);' : ''); ?>">
                                <td style="padding:6px 4px;font-size:12px;color:#6b7280;">
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($index === 0): ?> 🥇
                                    <?php elseif($index === 1): ?> 🥈
                                    <?php elseif($index === 2): ?> 🥉
                                    <?php else: ?> <?php echo e($index + 1); ?>

                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </td>
                                <td style="padding:6px 4px;font-size:12px;color:#e5e7eb;font-weight:500;"><?php echo e(Str::limit($ps->product_name, 35)); ?></td>
                                <td style="padding:6px 4px;text-align:center;">
                                    <span style="background:#22c55e;color:#fff;font-size:11px;font-weight:700;padding:2px 8px;border-radius:10px;"><?php echo e($ps->total_qty); ?></span>
                                </td>
                                <td style="padding:6px 4px;text-align:right;font-size:12px;color:#f59e0b;font-weight:600;"><?php echo e(number_format($ps->total_revenue, 0)); ?> ₺</td>
                            </tr>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                            <tr>
                                <td colspan="4" style="padding:20px;text-align:center;color:#6b7280;font-size:13px;">Henüz teslim edilen sipariş yok</td>
                            </tr>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        
        <div style="background:#111827;border-radius:12px;padding:20px;border:1px solid rgba(255,255,255,0.08);">
            <h3 style="font-size:14px;font-weight:700;color:#fff;margin-bottom:16px;">🔄 İade Edilen Ürünler</h3>
            <div style="max-height:350px;overflow-y:auto;">
                <table style="width:100%;border-collapse:collapse;">
                    <thead>
                        <tr style="border-bottom:1px solid rgba(255,255,255,0.1);">
                            <th style="text-align:left;padding:6px 4px;font-size:10px;color:#9ca3af;font-weight:600;">#</th>
                            <th style="text-align:left;padding:6px 4px;font-size:10px;color:#9ca3af;font-weight:600;">Ürün</th>
                            <th style="text-align:center;padding:6px 4px;font-size:10px;color:#9ca3af;font-weight:600;">Adet</th>
                            <th style="text-align:right;padding:6px 4px;font-size:10px;color:#9ca3af;font-weight:600;">Tutar</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $returnSummary; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $rs): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                            <tr style="border-bottom:1px solid rgba(255,255,255,0.05);background:rgba(239,68,68,0.05);">
                                <td style="padding:6px 4px;font-size:12px;color:#6b7280;"><?php echo e($index + 1); ?></td>
                                <td style="padding:6px 4px;font-size:12px;color:#e5e7eb;font-weight:500;"><?php echo e(Str::limit($rs->product_name, 35)); ?></td>
                                <td style="padding:6px 4px;text-align:center;">
                                    <span style="background:#ef4444;color:#fff;font-size:11px;font-weight:700;padding:2px 8px;border-radius:10px;"><?php echo e($rs->total_qty); ?></span>
                                </td>
                                <td style="padding:6px 4px;text-align:right;font-size:12px;color:#ef4444;font-weight:600;"><?php echo e(number_format($rs->total_revenue, 0)); ?> ₺</td>
                            </tr>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                            <tr>
                                <td colspan="4" style="padding:20px;text-align:center;color:#6b7280;font-size:13px;">🎉 Henüz iade yok</td>
                            </tr>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        
        <div style="background:#111827;border-radius:12px;padding:20px;border:1px solid rgba(255,255,255,0.08);">
            <h3 style="font-size:14px;font-weight:700;color:#fff;margin-bottom:16px;">👟 En Çok Satılan Bedenler</h3>
            <div style="max-height:350px;overflow-y:auto;">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $sizeSummary; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $size): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                    <?php
                        $maxQty = $sizeSummary->max('total_qty') ?: 1;
                        $percentage = ($size->total_qty / $maxQty) * 100;
                        $sizeLabel = str_replace('Beden: ', '', $size->variant_info);
                    ?>
                    <div style="margin-bottom:10px;">
                        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:4px;">
                            <span style="font-size:13px;color:#e5e7eb;font-weight:500;"><?php echo e($sizeLabel); ?></span>
                            <span style="font-size:12px;color:#22c55e;font-weight:700;"><?php echo e($size->total_qty); ?> adet</span>
                        </div>
                        <div style="background:rgba(255,255,255,0.05);border-radius:6px;height:8px;overflow:hidden;">
                            <div style="background:linear-gradient(90deg,#22c55e,#16a34a);height:100%;border-radius:6px;width:<?php echo e($percentage); ?>%;transition:width 0.5s;"></div>
                        </div>
                    </div>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                    <p style="color:#6b7280;font-size:13px;text-align:center;padding:20px;">Veri yok</p>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
        </div>
    </div>

    
    <div style="background:#111827;border-radius:12px;padding:20px;border:1px solid rgba(255,255,255,0.08);">
        <h3 style="font-size:14px;font-weight:700;color:#fff;margin-bottom:4px;">📋 Satış & İade Detay Tablosu</h3>
        <p style="font-size:12px;color:#6b7280;margin-bottom:16px;">Teslim edilen ve iade edilen tüm satışlar. Durum filtresini kullanarak sadece iadeleri görebilirsiniz.</p>
        <?php echo e($this->table); ?>

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
<?php /**PATH C:\Users\Lenovo\Desktop\Projelerim\patenliayakkabilar.com\resources\views\filament\pages\product-sales-report.blade.php ENDPATH**/ ?>