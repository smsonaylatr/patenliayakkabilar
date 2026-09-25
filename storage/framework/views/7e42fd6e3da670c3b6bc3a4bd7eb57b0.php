<div class="bg-white dark:bg-gray-900 shadow-sm rounded-xl border border-gray-200 dark:border-gray-800 p-6 mb-6">
    <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">📈 Son 30 Günlük Stok Hareketleri</h3>
    
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(array_sum($inData) === 0 && array_sum($outData) === 0): ?>
        <div class="flex items-center justify-center h-[200px] text-gray-400">
            <p>Henüz stok hareketi bulunmuyor.</p>
        </div>
    <?php else: ?>
        <div style="position:relative;width:100%;height:280px;" wire:ignore>
            <canvas id="stockChart" style="width:100%;height:280px;"></canvas>
        </div>

        <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.7/dist/chart.umd.min.js"></script>
        <script>
            (function() {
                function initChart() {
                    var canvas = document.getElementById('stockChart');
                    if (!canvas) return;
                    var ctx = canvas.getContext('2d');
                    if (!ctx) return;

                    new Chart(ctx, {
                        type: 'bar',
                        data: {
                            labels: <?php echo json_encode($labels, 15, 512) ?>,
                            datasets: [
                                {
                                    label: 'Giriş (Yenileme/İptal/İade)',
                                    data: <?php echo json_encode($inData, 15, 512) ?>,
                                    backgroundColor: 'rgba(34, 197, 94, 0.7)',
                                    borderRadius: 3,
                                },
                                {
                                    label: 'Çıkış (Satış)',
                                    data: <?php echo json_encode($outData, 15, 512) ?>,
                                    backgroundColor: 'rgba(239, 68, 68, 0.7)',
                                    borderRadius: 3,
                                }
                            ]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            scales: {
                                y: { beginAtZero: true, ticks: { stepSize: 1 } }
                            },
                            plugins: {
                                legend: { position: 'top' }
                            }
                        }
                    });
                }

                if (typeof Chart !== 'undefined') {
                    initChart();
                } else {
                    document.addEventListener('DOMContentLoaded', function() {
                        setTimeout(initChart, 500);
                    });
                }
            })();
        </script>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
</div>
<?php /**PATH C:\Users\Lenovo\Desktop\Projelerim\patenliayakkabilar.com\resources\views\livewire\admin\stock-chart.blade.php ENDPATH**/ ?>