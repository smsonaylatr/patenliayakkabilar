<div class="bg-white dark:bg-gray-900 shadow-sm rounded-xl border border-gray-200 dark:border-gray-800 p-6 mb-8">
    <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">📈 Son 30 Günlük Stok Hareketleri</h3>
    
    <div class="relative w-full h-[300px]" wire:ignore>
        <canvas id="stockChart"></canvas>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('livewire:initialized', () => {
            const ctx = document.getElementById('stockChart').getContext('2d');
            
            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: @json($labels),
                    datasets: [
                        {
                            label: 'Giriş (Yenileme, İptal, İade)',
                            data: @json($inData),
                            backgroundColor: 'rgba(34, 197, 94, 0.8)',
                            borderRadius: 4,
                        },
                        {
                            label: 'Çıkış (Satış)',
                            data: @json($outData),
                            backgroundColor: 'rgba(239, 68, 68, 0.8)',
                            borderRadius: 4,
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                stepSize: 1
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            position: 'top',
                            labels: {
                                font: {
                                    family: "'Inter', sans-serif"
                                }
                            }
                        }
                    }
                }
            });
        });
    </script>
</div>
