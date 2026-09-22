<x-filament-panels::page>
    {{-- Dashboard Üst Bölüm: İstatistik Kartları --}}
    <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:16px;margin-bottom:24px;">
        {{-- Toplam Satılan Adet --}}
        <div style="background:#111827;border-radius:12px;padding:20px;border:1px solid rgba(255,255,255,0.08);">
            <div style="display:flex;align-items:center;gap:10px;margin-bottom:8px;">
                <span style="font-size:24px;">📦</span>
                <span style="font-size:12px;color:#9ca3af;font-weight:600;">Toplam Satılan Adet</span>
            </div>
            <div style="font-size:28px;font-weight:800;color:#22c55e;">{{ number_format($totalDelivered) }}</div>
        </div>

        {{-- Toplam Ciro --}}
        <div style="background:#111827;border-radius:12px;padding:20px;border:1px solid rgba(255,255,255,0.08);">
            <div style="display:flex;align-items:center;gap:10px;margin-bottom:8px;">
                <span style="font-size:24px;">💰</span>
                <span style="font-size:12px;color:#9ca3af;font-weight:600;">Toplam Ciro</span>
            </div>
            <div style="font-size:28px;font-weight:800;color:#f59e0b;">{{ number_format($totalRevenue, 2) }} ₺</div>
        </div>

        {{-- Benzersiz Müşteri --}}
        <div style="background:#111827;border-radius:12px;padding:20px;border:1px solid rgba(255,255,255,0.08);">
            <div style="display:flex;align-items:center;gap:10px;margin-bottom:8px;">
                <span style="font-size:24px;">👥</span>
                <span style="font-size:12px;color:#9ca3af;font-weight:600;">Benzersiz Müşteri</span>
            </div>
            <div style="font-size:28px;font-weight:800;color:#3b82f6;">{{ number_format($uniqueCustomers) }}</div>
        </div>

        {{-- Satılan Ürün Çeşidi --}}
        <div style="background:#111827;border-radius:12px;padding:20px;border:1px solid rgba(255,255,255,0.08);">
            <div style="display:flex;align-items:center;gap:10px;margin-bottom:8px;">
                <span style="font-size:24px;">👟</span>
                <span style="font-size:12px;color:#9ca3af;font-weight:600;">Satılan Ürün Çeşidi</span>
            </div>
            <div style="font-size:28px;font-weight:800;color:#a855f7;">{{ number_format($uniqueProducts) }}</div>
        </div>
    </div>

    {{-- Orta Bölüm: Ürün Özeti ve Beden Dağılımı --}}
    <div style="display:grid;grid-template-columns:2fr 1fr;gap:16px;margin-bottom:24px;">

        {{-- SOL: Ürün Bazlı Satış Özeti --}}
        <div style="background:#111827;border-radius:12px;padding:20px;border:1px solid rgba(255,255,255,0.08);">
            <h3 style="font-size:14px;font-weight:700;color:#fff;margin-bottom:16px;">🏆 Ürün Bazlı Satış Özeti (Teslim Edilen)</h3>
            <div style="max-height:350px;overflow-y:auto;">
                <table style="width:100%;border-collapse:collapse;">
                    <thead>
                        <tr style="border-bottom:1px solid rgba(255,255,255,0.1);">
                            <th style="text-align:left;padding:8px 6px;font-size:11px;color:#9ca3af;font-weight:600;">#</th>
                            <th style="text-align:left;padding:8px 6px;font-size:11px;color:#9ca3af;font-weight:600;">Ürün</th>
                            <th style="text-align:center;padding:8px 6px;font-size:11px;color:#9ca3af;font-weight:600;">Adet</th>
                            <th style="text-align:center;padding:8px 6px;font-size:11px;color:#9ca3af;font-weight:600;">Sipariş</th>
                            <th style="text-align:right;padding:8px 6px;font-size:11px;color:#9ca3af;font-weight:600;">Ciro</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($productSummary as $index => $ps)
                            <tr style="border-bottom:1px solid rgba(255,255,255,0.05);{{ $index < 3 ? 'background:rgba(34,197,94,0.05);' : '' }}">
                                <td style="padding:8px 6px;font-size:12px;color:#6b7280;">
                                    @if($index === 0) 🥇
                                    @elseif($index === 1) 🥈
                                    @elseif($index === 2) 🥉
                                    @else {{ $index + 1 }}
                                    @endif
                                </td>
                                <td style="padding:8px 6px;font-size:13px;color:#e5e7eb;font-weight:500;">{{ Str::limit($ps->product_name, 45) }}</td>
                                <td style="padding:8px 6px;text-align:center;">
                                    <span style="background:#22c55e;color:#fff;font-size:12px;font-weight:700;padding:2px 10px;border-radius:10px;">{{ $ps->total_qty }}</span>
                                </td>
                                <td style="padding:8px 6px;text-align:center;font-size:12px;color:#9ca3af;">{{ $ps->order_count }}</td>
                                <td style="padding:8px 6px;text-align:right;font-size:13px;color:#f59e0b;font-weight:600;">{{ number_format($ps->total_revenue, 2) }} ₺</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" style="padding:20px;text-align:center;color:#6b7280;font-size:13px;">Henüz teslim edilen sipariş yok</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- SAĞ: En Çok Satılan Bedenler --}}
        <div style="background:#111827;border-radius:12px;padding:20px;border:1px solid rgba(255,255,255,0.08);">
            <h3 style="font-size:14px;font-weight:700;color:#fff;margin-bottom:16px;">👟 En Çok Satılan Bedenler</h3>
            <div style="max-height:350px;overflow-y:auto;">
                @forelse($sizeSummary as $size)
                    @php
                        $maxQty = $sizeSummary->max('total_qty') ?: 1;
                        $percentage = ($size->total_qty / $maxQty) * 100;
                        $sizeLabel = str_replace('Beden: ', '', $size->variant_info);
                    @endphp
                    <div style="margin-bottom:10px;">
                        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:4px;">
                            <span style="font-size:13px;color:#e5e7eb;font-weight:500;">{{ $sizeLabel }}</span>
                            <span style="font-size:12px;color:#22c55e;font-weight:700;">{{ $size->total_qty }} adet</span>
                        </div>
                        <div style="background:rgba(255,255,255,0.05);border-radius:6px;height:8px;overflow:hidden;">
                            <div style="background:linear-gradient(90deg,#22c55e,#16a34a);height:100%;border-radius:6px;width:{{ $percentage }}%;transition:width 0.5s;"></div>
                        </div>
                    </div>
                @empty
                    <p style="color:#6b7280;font-size:13px;text-align:center;padding:20px;">Veri yok</p>
                @endforelse
            </div>
        </div>
    </div>

    {{-- Alt Bölüm: Detay Tablosu --}}
    <div style="background:#111827;border-radius:12px;padding:20px;border:1px solid rgba(255,255,255,0.08);">
        <h3 style="font-size:14px;font-weight:700;color:#fff;margin-bottom:16px;">📋 Teslim Edilen Satışlar — Detay Tablosu</h3>
        <p style="font-size:12px;color:#6b7280;margin-bottom:16px;">Hangi ürün, hangi beden, kime satılmış? Filtreleme ve arama ile hızlıca bulun.</p>
        {{ $this->table }}
    </div>
</x-filament-panels::page>
