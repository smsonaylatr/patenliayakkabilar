<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tedarikçi Sipariş İrsaliyesi - Patenli Ayakkabılar</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @media print {
            .no-print { display: none !important; }
            body { background: #fff !important; padding: 0 !important; }
            .print-container { max-width: 100% !important; margin: 0 !important; box-shadow: none !important; border: none !important; }
            .page-break { page-break-before: always; }
            tr, .order-card { page-break-inside: avoid; }
        }
    </style>
</head>
<body class="bg-slate-100 text-slate-800 font-sans antialiased p-4 md:p-8">

    
    <div class="no-print max-w-4xl mx-auto mb-6 bg-white p-4 rounded-xl shadow-md border border-slate-200 flex flex-wrap items-center justify-between gap-4">
        <div class="flex items-center gap-3">
            <span class="text-3xl">📦</span>
            <div>
                <h1 class="font-bold text-slate-900 text-lg">Tedarikçi Sipariş İrsaliyesi</h1>
                <p class="text-xs text-slate-500">Seçilen <?php echo e($totalOrders); ?> sipariş için hazırlık ve paketleme listesi</p>
            </div>
        </div>
        <div class="flex items-center gap-3">
            <a href="/admin/orders" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-sm font-semibold rounded-lg transition">
                ⬅️ Siparişlere Dön
            </a>
            <a href="<?php echo e(route('admin.orders.supplier-waybill.download', ['ids' => request('ids')])); ?>" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-lg shadow-sm transition flex items-center gap-1.5">
                📥 PDF Olarak İndir
            </a>
            <button onclick="window.print()" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold rounded-lg shadow-sm transition flex items-center gap-1.5">
                🖨️ Yazdır / PDF Kaydet
            </button>
        </div>
    </div>

    
    <div class="print-container max-w-4xl mx-auto bg-white rounded-2xl shadow-lg border border-slate-200 overflow-hidden">
        
        
        <div class="bg-slate-900 text-white p-6 md:p-8 flex justify-between items-center border-b border-slate-800">
            <div>
                <h1 class="text-2xl font-bold tracking-tight">📦 Tedarikçi Sipariş İrsaliyesi</h1>
                <p class="text-sm text-slate-400 mt-1">Patenli Ayakkabılar · patenliayakkabilar.com</p>
            </div>
            <div class="text-right">
                <div class="text-xs uppercase tracking-wider text-slate-400">Tarih</div>
                <div class="text-base font-semibold mt-0.5"><?php echo e($date); ?></div>
            </div>
        </div>

        
        <div class="grid grid-cols-3 bg-slate-50 border-b border-slate-200 divide-x divide-slate-200 text-center p-4">
            <div>
                <div class="text-2xl font-black text-indigo-600"><?php echo e($totalOrders); ?></div>
                <div class="text-xs uppercase font-semibold text-slate-500 mt-1">Sipariş Sayısı</div>
            </div>
            <div>
                <div class="text-2xl font-black text-indigo-600"><?php echo e($totalProducts); ?></div>
                <div class="text-xs uppercase font-semibold text-slate-500 mt-1">Ürün / Varyant</div>
            </div>
            <div>
                <div class="text-2xl font-black text-amber-600"><?php echo e($totalQuantity); ?></div>
                <div class="text-xs uppercase font-semibold text-slate-500 mt-1">Toplam Adet</div>
            </div>
        </div>

        <div class="p-6 md:p-8 space-y-8">
            
            
            <div>
                <div class="flex items-center justify-between mb-4 border-b-2 border-indigo-600 pb-2">
                    <h2 class="text-lg font-bold text-slate-900 flex items-center gap-2">
                        <span>📋</span> Konsolide Ürün Özeti (Tedarikçi Hazırlık Listesi)
                    </h2>
                    <span class="text-xs text-slate-500">Tüm seçili siparişlerin toplamı</span>
                </div>

                <div class="border border-slate-200 rounded-xl overflow-hidden shadow-sm">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-indigo-600 text-white text-xs uppercase font-semibold tracking-wider">
                                <th class="p-3.5 text-center w-36">Görsel</th>
                                <th class="p-3.5 text-center">Numara / Beden</th>
                                <th class="p-3.5 text-center w-36">Toplam Adet</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-sm">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $consolidated; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                                <tr class="hover:bg-slate-50 transition">
                                    <td class="p-3 text-center">
                                        <img src="<?php echo e($item['image']); ?>" alt="Ürün" class="w-20 h-20 md:w-24 md:h-24 object-cover rounded-xl border border-slate-200 shadow-sm mx-auto">
                                    </td>
                                    <td class="p-3 text-center">
                                        <span class="inline-block bg-indigo-50 text-indigo-800 border border-indigo-200 font-black text-sm md:text-base px-4 py-2 rounded-xl">
                                            <?php echo e($item['variant'] ?: '-'); ?>

                                        </span>
                                    </td>
                                    <td class="p-3 text-center">
                                        <span class="inline-block bg-amber-500 text-white font-black text-base md:text-lg px-4 py-2 rounded-xl shadow-sm">
                                            x<?php echo e($item['quantity']); ?>

                                        </span>
                                    </td>
                                </tr>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                            <tr class="bg-slate-100 font-bold text-slate-900">
                                <td colspan="2" class="p-4 text-right text-base">GENEL TOPLAM:</td>
                                <td class="p-4 text-center">
                                    <span class="inline-block bg-slate-900 text-white font-black text-lg px-5 py-2 rounded-xl">
                                        x<?php echo e($totalQuantity); ?>

                                    </span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            
            <div>
                <div class="flex items-center justify-between mb-4 border-b-2 border-indigo-600 pb-2">
                    <h2 class="text-lg font-bold text-slate-900 flex items-center gap-2">
                        <span>🧾</span> Sipariş Detayları
                    </h2>
                    <span class="text-xs text-slate-500">Sipariş bazında ayrılmış paket listesi</span>
                </div>

                <div class="space-y-4">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $orders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $order): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                        <div class="order-card border border-slate-200 rounded-xl overflow-hidden shadow-sm">
                            <div class="bg-slate-50 p-3.5 border-b border-slate-200 flex flex-wrap items-center justify-between gap-2">
                                <div class="flex items-center gap-3">
                                    <span class="font-black text-indigo-700 text-base">#<?php echo e($order['order_number']); ?></span>
                                    <span class="font-bold text-slate-800 text-sm"><?php echo e($order['customer_name']); ?></span>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!empty($order['customer_phone'])): ?>
                                        <span class="text-xs text-slate-500 font-mono">(<?php echo e($order['customer_phone']); ?>)</span>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </div>
                                <div class="text-xs font-semibold text-slate-600">
                                    📍 <?php echo e($order['city']); ?> <?php echo e($order['district'] ? '/ ' . $order['district'] : ''); ?> · 🕒 <?php echo e($order['date']); ?>

                                </div>
                            </div>

                            <table class="w-full text-left border-collapse">
                                <thead>
                                    <tr class="bg-slate-100 text-slate-600 text-xs uppercase font-semibold">
                                        <th class="p-2.5 text-center w-28">Görsel</th>
                                        <th class="p-2.5 text-center">Numara / Beden</th>
                                        <th class="p-2.5 text-center w-28">Adet</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100 text-sm">
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $order['items']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                                        <tr>
                                            <td class="p-2.5 text-center">
                                                <img src="<?php echo e($item['image']); ?>" alt="Ürün" class="w-16 h-16 object-cover rounded-lg border border-slate-200 shadow-sm mx-auto">
                                            </td>
                                            <td class="p-2.5 text-center">
                                                <span class="inline-block bg-indigo-50 text-indigo-700 border border-indigo-200 font-bold text-sm px-3 py-1 rounded-lg">
                                                    <?php echo e($item['variant'] ?: '-'); ?>

                                                </span>
                                            </td>
                                            <td class="p-2.5 text-center">
                                                <span class="inline-block bg-amber-500 text-white font-bold text-sm px-3.5 py-1 rounded-lg shadow-sm">
                                                    x<?php echo e($item['quantity']); ?>

                                                </span>
                                            </td>
                                        </tr>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                </div>
            </div>

        </div>

        
        <div class="bg-slate-50 border-t border-slate-200 p-4 text-center text-xs text-slate-500">
            Patenli Ayakkabılar · www.patenliayakkabilar.com · Bu belge tedarikçi sipariş hazırlığı için sistem tarafından otomatik oluşturulmuştur.
        </div>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(request('auto_print')): ?>
        <script>
            window.addEventListener('load', function() {
                setTimeout(function() {
                    window.print();
                }, 400);
            });
        </script>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
</body>
</html>
<?php /**PATH C:\Users\Lenovo\Desktop\Projelerim\patenliayakkabilar.com\resources\views\admin\orders\supplier-waybill-print.blade.php ENDPATH**/ ?>