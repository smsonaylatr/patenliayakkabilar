<div class="space-y-4 text-slate-200">
    {{-- Üst Bilgilendirme ve Hızlı IP İşlemi --}}
    <div style="background: rgba(15, 23, 42, 0.7); border: 1px solid rgba(255, 255, 255, 0.08); border-radius: 12px; padding: 16px;">
        <div style="display: flex; align-items: center; justify-content: space-between; gap: 12px; flex-wrap: wrap;">
            <div>
                <div style="font-size: 13px; font-weight: 800; color: #f8fafc; display: flex; align-items: center; gap: 6px;">
                    <span>🛡️</span> Hızlı IP Engeli Yönetimi
                </div>
                <div style="font-size: 11px; color: #94a3b8; margin-top: 2px;">
                    Belirli bir IP adresinin engelini manuel açabilir veya yeni bir IP'yi engelleyebilirsiniz.
                </div>
            </div>
            <div style="display: flex; align-items: center; gap: 8px; flex-wrap: wrap;" x-data="{ manualIp: '' }">
                <input 
                    type="text" 
                    x-model="manualIp" 
                    placeholder="Örn: 184.23.180.224" 
                    style="background: #0f172a; border: 1px solid rgba(255, 255, 255, 0.15); border-radius: 8px; padding: 6px 12px; font-size: 12px; color: #fff; width: 170px;"
                />
                <button 
                    type="button" 
                    @click="if(manualIp) { $wire.unblockIp(manualIp); manualIp = ''; }" 
                    style="background: #10b981; color: #fff; border: none; border-radius: 8px; padding: 6px 12px; font-size: 12px; font-weight: 700; cursor: pointer; display: inline-flex; align-items: center; gap: 4px;"
                    title="Bu IP'nin engelini aç"
                >
                    ✅ Engeli Aç
                </button>
                <button 
                    type="button" 
                    @click="if(manualIp && confirm('Bu IP adresini engellemek istediğinize emin misiniz?')) { $wire.blockCustomIp(manualIp); manualIp = ''; }" 
                    style="background: #ef4444; color: #fff; border: none; border-radius: 8px; padding: 6px 12px; font-size: 12px; font-weight: 700; cursor: pointer; display: inline-flex; align-items: center; gap: 4px;"
                    title="Bu IP'yi engelle"
                >
                    ⛔ Engelle
                </button>
            </div>
        </div>
    </div>

    {{-- Engellenen Ziyaretçiler Listesi --}}
    @if ($blockedVisitors->isEmpty())
        <div style="padding: 36px 20px; text-align: center; background: rgba(16, 185, 129, 0.05); border: 1px dashed rgba(16, 185, 129, 0.3); border-radius: 12px;">
            <div style="font-size: 32px; margin-bottom: 8px;">🎉</div>
            <div style="font-size: 14px; font-weight: 800; color: #10b981;">Erişimi Engellenmiş Ziyaretçi Yok</div>
            <div style="font-size: 12px; color: #94a3b8; margin-top: 4px;">
                Şu anda sitede kısıtlanmış veya engellenmiş hiçbir kullanıcı / IP adresi bulunmuyor.
            </div>
        </div>
    @else
        <div style="overflow-x: auto; border: 1px solid rgba(255, 255, 255, 0.08); border-radius: 12px; background: #0b1120;">
            <table style="width: 100%; border-collapse: collapse; font-size: 12px; text-align: left;">
                <thead>
                    <tr style="background: rgba(30, 41, 59, 0.8); border-bottom: 1px solid rgba(255, 255, 255, 0.08); color: #94a3b8; font-size: 11px; text-transform: uppercase; letter-spacing: 0.05em;">
                        <th style="padding: 10px 14px;">Ziyaretçi & IP</th>
                        <th style="padding: 10px 14px;">Cihaz / Tarayıcı</th>
                        <th style="padding: 10px 14px;">Son Bulunduğu Sayfa</th>
                        <th style="padding: 10px 14px;">Engellenme Zamanı</th>
                        <th style="padding: 10px 14px; text-align: right;">İşlem</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($blockedVisitors as $visitor)
                        @php
                            $name = $visitor->user_id && $visitor->user ? $visitor->user->name : $visitor->display_name;
                            $deviceIcon = match ($visitor->device_type) {
                                'mobile' => '📱',
                                'tablet' => '📟',
                                default => '💻',
                            };
                            $timeStr = $visitor->updated_at ? $visitor->updated_at->format('d.m.Y H:i') : ($visitor->last_heartbeat_at ? $visitor->last_heartbeat_at->format('d.m.Y H:i') : '-');
                        @endphp
                        <tr style="border-bottom: 1px solid rgba(255, 255, 255, 0.05); transition: background 0.15s ease;" onmouseover="this.style.background='rgba(255,255,255,0.02)'" onmouseout="this.style.background='transparent'">
                            <td style="padding: 12px 14px;">
                                <div style="display: flex; align-items: center; gap: 8px;">
                                    <span style="display: inline-flex; align-items: center; justify-content: center; width: 28px; height: 28px; border-radius: 50%; background: rgba(239, 68, 68, 0.2); color: #ef4444; font-weight: 800; font-size: 12px; border: 1px solid rgba(239, 68, 68, 0.4);">
                                        ⛔
                                    </span>
                                    <div>
                                        <div style="font-weight: 800; color: #f8fafc; display: flex; align-items: center; gap: 5px;">
                                            <span>{{ $name }}</span>
                                            {!! $visitor->stars_html !!}
                                            <span style="font-family: monospace; color: #38bdf8; font-size: 10px;">#{{ $visitor->guest_id }}</span>
                                        </div>
                                        <div style="font-family: monospace; color: #fb7185; font-size: 11px;">
                                            {{ $visitor->ip_address ?: 'IP Bilinmiyor' }}
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td style="padding: 12px 14px; color: #94a3b8;">
                                <span>{{ $deviceIcon }} {{ $visitor->browser ?? 'Bilinmiyor' }}</span>
                                <div style="font-size: 10px; color: #64748b;">{{ $visitor->operating_system ?? '' }}</div>
                            </td>
                            <td style="padding: 12px 14px; max-width: 200px;">
                                <div style="color: #cbd5e1; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;" title="{{ $visitor->current_title ?: $visitor->current_path }}">
                                    {{ $visitor->current_title ?: $visitor->current_path }}
                                </div>
                                <div style="font-size: 10px; color: #64748b; font-family: monospace;">
                                    {{ $visitor->current_path }}
                                </div>
                            </td>
                            <td style="padding: 12px 14px; color: #94a3b8; font-size: 11px;">
                                <div>{{ $timeStr }}</div>
                                <div style="font-size: 10px; color: #64748b;">
                                    {{ $visitor->updated_at ? $visitor->updated_at->diffForHumans() : '' }}
                                </div>
                            </td>
                            <td style="padding: 12px 14px; text-align: right;">
                                <button 
                                    type="button" 
                                    wire:click="unblockVisitorById({{ $visitor->id }})" 
                                    wire:loading.attr="disabled"
                                    style="background: #10b981; color: #ffffff; border: none; border-radius: 8px; padding: 6px 14px; font-size: 11.5px; font-weight: 800; cursor: pointer; display: inline-flex; align-items: center; gap: 5px; box-shadow: 0 2px 8px rgba(16, 185, 129, 0.3); transition: all 0.2s ease;"
                                    onmouseover="this.style.background='#059669'"
                                    onmouseout="this.style.background='#10b981'"
                                >
                                    <span>✅</span> Engeli Kaldır
                                </button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>
