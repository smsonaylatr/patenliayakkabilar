<div>
    <div class="mb-4">
        <h4 class="font-bold text-lg mb-2">Beden Stokları</h4>
        <div class="flex flex-wrap gap-2">
            @foreach($product->variants as $variant)
                <div class="p-2 border rounded dark:border-gray-700 bg-gray-50 dark:bg-gray-800 text-center min-w-[60px]">
                    <div class="text-xs text-gray-500">{{ $variant->size }}</div>
                    <div class="font-bold {{ $variant->stock <= 0 ? 'text-danger-600' : ($variant->stock <= 3 ? 'text-warning-600' : 'text-success-600') }}">
                        {{ $variant->stock }}
                    </div>
                </div>
            @endforeach
        </div>
        <div class="mt-2 font-bold">
            Toplam Stok: {{ $product->stock }}
        </div>
    </div>

    <div>
        <h4 class="font-bold text-lg mb-2">Son 10 Stok Hareketi</h4>
        @if($movements->isEmpty())
            <p class="text-sm text-gray-500">Henüz stok hareketi bulunmuyor.</p>
        @else
            <table class="w-full text-sm text-left border-collapse">
                <thead>
                    <tr class="bg-gray-100 dark:bg-gray-800">
                        <th class="p-2 border dark:border-gray-700">Tarih</th>
                        <th class="p-2 border dark:border-gray-700">Beden</th>
                        <th class="p-2 border dark:border-gray-700">Değişim</th>
                        <th class="p-2 border dark:border-gray-700">Not</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($movements as $movement)
                        <tr>
                            <td class="p-2 border dark:border-gray-700">{{ $movement->created_at->format('d.m.Y H:i') }}</td>
                            <td class="p-2 border dark:border-gray-700">{{ $movement->variant?->size ?? '-' }}</td>
                            <td class="p-2 border dark:border-gray-700 font-bold {{ $movement->quantity > 0 && $movement->type === App\Models\StockMovement::TYPE_RESTOCK ? 'text-success-600' : ($movement->quantity < 0 ? 'text-danger-600' : '') }}">
                                {{ $movement->new_stock - $movement->old_stock > 0 ? '+' : '' }}{{ $movement->new_stock - $movement->old_stock }}
                            </td>
                            <td class="p-2 border dark:border-gray-700">{{ $movement->note }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>
</div>
