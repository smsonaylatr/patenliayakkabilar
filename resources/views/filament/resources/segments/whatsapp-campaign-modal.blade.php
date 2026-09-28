@php
    $siteUrl = rtrim(config('app.url', 'https://patenliayakkabilar.com'), '/');
    $visitorsData = $visitors->map(function ($v) use ($siteUrl) {
        return [
            'id' => $v->id,
            'name' => $v->display_name,
            'phone' => $v->formatted_phone ?: $v->contact_phone,
            'clean_phone' => $v->clean_whatsapp_phone,
            'visit_count' => (int) ($v->visit_count ?: 1),
            'stars' => $v->stars_count,
            'cart_items' => (int) ($v->cart_items_count ?: 0),
            'cart_total' => (float) ($v->cart_total ?: 0),
            'cart_total_formatted' => number_format((float) ($v->cart_total ?: 0), 2) . ' ₺',
            'product_name' => $v->interested_product_info['name'] ?? null,
            'intent_score' => (int) ($v->intent_score ?? 15),
            'is_member' => (bool) $v->user_id,
        ];
    })->filter(fn($v) => !empty($v['clean_phone']))->values()->toArray();

    $totalReachable = count($visitorsData);
    $cartCount = collect($visitorsData)->where('cart_items', '>', 0)->count();
    $cartSum = collect($visitorsData)->sum('cart_total');
    $threeStarsCount = collect($visitorsData)->where('visit_count', '>=', 3)->count();
@endphp

<div
    x-data="{
        siteUrl: '{{ $siteUrl }}',
        visitors: {{ json_encode($visitorsData, JSON_UNESCAPED_UNICODE) }},
        selectedFilter: 'all',
        searchTerm: '',
        couponCode: 'SADIK10',
        activeTemplate: 'cart',
        templates: {
            cart: 'Merhaba {isim}, Patenli Ayakkabılar sepetinizde bekleyen ürünleriniz için size özel %10 indirim kuponunuz tanımlandı: *{kupon}* ⛸️\n\nSepetinizi buradan kolayca tamamlayabilirsiniz: {site_url}/sepet?utm_source=whatsapp&utm_medium=chat&utm_campaign=sepet_firsat\n\nBeden veya ürün detaylarıyla ilgili yardımcı olabileceğimiz bir konu var mı?',
            product: 'Merhaba {isim}, Patenli Ayakkabılar mağazamızda incelediğiniz modeller hakkında yardımcı olmak isteriz! ⛸️\n\nDoğru numara seçimi ve aynı gün kargo avantajları için buradayız. Size özel *{kupon}* koduyla %10 indirim tanımlayabiliriz.',
            vip: 'Merhaba {isim}, Patenli Ayakkabılar mağazamızı tekrar ziyaret ettiğiniz için teşekkür ederiz! ⭐\n\nMüdavim ziyaretçilerimize özel *VIP15* koduyla tüm modellerde geçerli %15 indirim kazandınız!\n\nModelleri incelemek ve sipariş vermek için: {site_url}/?utm_source=whatsapp&utm_medium=chat&utm_campaign=vip_ziyaretci',
            shipping: 'Merhaba {isim}, Patenli Ayakkabılar siparişlerinizde bugün geçerli ÜCRETSİZ HIZLI KARGO fırsatı başladı! 🚀\n\nAyrıca *{kupon}* koduyla anında %10 indirim avantajınız bulunuyor. Yardımcı olabileceğimiz bir model var mı?'
        },
        customMessage: '',
        copiedType: null,
        init() {
            this.customMessage = this.templates.cart;
        },
        setTemplate(key) {
            this.activeTemplate = key;
            this.customMessage = this.templates[key] || '';
            if (key === 'vip') {
                this.couponCode = 'VIP15';
            } else if (this.couponCode === 'VIP15') {
                this.couponCode = 'SADIK10';
            }
        },
        get filteredVisitors() {
            var self = this;
            return self.visitors.filter(function(v) {
                // Filter pill
                if (self.selectedFilter === 'cart' && v.cart_items <= 0) return false;
                if (self.selectedFilter === 'three_stars' && v.visit_count < 3) return false;
                if (self.selectedFilter === 'high_intent' && v.intent_score < 60) return false;
                if (self.selectedFilter === 'member' && !v.is_member) return false;

                // Search term
                if (self.searchTerm.trim() !== '') {
                    var s = self.searchTerm.toLowerCase();
                    var n = (v.name || '').toLowerCase();
                    var p = (v.phone || '').toLowerCase();
                    return n.includes(s) || p.includes(s);
                }
                return true;
            });
        },
        getMessageFor(name) {
            var msg = this.customMessage || '';
            msg = msg.replaceAll('{isim}', name || 'Değerli Müşterimiz');
            msg = msg.replaceAll('{kupon}', this.couponCode);
            msg = msg.replaceAll('{site_url}', this.siteUrl);
            return msg;
        },
        getWhatsAppUrl(cleanPhone, name) {
            var text = this.getMessageFor(name);
            return 'https://wa.me/' + cleanPhone + '?text=' + encodeURIComponent(text);
        },
        copyPhoneList() {
            var self = this;
            var phones = self.filteredVisitors.map(function(v) { return v.clean_phone; }).join(', ');
            navigator.clipboard.writeText(phones).then(function() {
                self.copiedType = 'phones';
                setTimeout(function() { self.copiedType = null; }, 3000);
            });
        },
        copyDetailedList() {
            var self = this;
            var lines = self.filteredVisitors.map(function(v) {
                return (v.name || 'Müşteri') + '\t' + v.phone + '\t' + v.visit_count + '. Ziyaret\tSepet: ' + v.cart_total_formatted;
            }).join('\n');
            navigator.clipboard.writeText(lines).then(function() {
                self.copiedType = 'detailed';
                setTimeout(function() { self.copiedType = null; }, 3000);
            });
        },
        copyMessage() {
            var self = this;
            navigator.clipboard.writeText(self.customMessage).then(function() {
                self.copiedType = 'message';
                setTimeout(function() { self.copiedType = null; }, 3000);
            });
        }
    }"
    style="display:flex;flex-direction:column;gap:18px;font-family:inherit;color:#f8fafc;"
>
    {{-- 1. KPI Özet Kartları --}}
    <div style="display:grid;grid-template-columns:repeat(auto-fit, minmax(160px, 1fr));gap:10px;">
        <div style="background:rgba(30,41,59,0.7);border:1px solid rgba(255,255,255,0.08);border-radius:10px;padding:12px;text-align:center;">
            <div style="font-size:10px;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:0.5px;">Ulaşılabilir Müşteri</div>
            <div style="font-size:22px;font-weight:800;color:#38bdf8;margin-top:2px;">{{ $totalReachable }}</div>
            <div style="font-size:10px;color:#64748b;">Telefonu kayıtlı 2-3 yıldız</div>
        </div>

        <div style="background:rgba(30,41,59,0.7);border:1px solid rgba(255,255,255,0.08);border-radius:10px;padding:12px;text-align:center;">
            <div style="font-size:10px;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:0.5px;">Sepeti Dolu Olanlar</div>
            <div style="font-size:22px;font-weight:800;color:#f43f5e;margin-top:2px;">{{ $cartCount }}</div>
            <div style="font-size:10px;color:#64748b;">Terk edilmiş sıcak sepetler</div>
        </div>

        <div style="background:rgba(30,41,59,0.7);border:1px solid rgba(255,255,255,0.08);border-radius:10px;padding:12px;text-align:center;">
            <div style="font-size:10px;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:0.5px;">Sepetteki Tutar</div>
            <div style="font-size:22px;font-weight:800;color:#34d399;margin-top:2px;">{{ number_format($cartSum, 2) }} ₺</div>
            <div style="font-size:10px;color:#64748b;">Kurtarılabilir ciro hacmi</div>
        </div>

        <div style="background:rgba(30,41,59,0.7);border:1px solid rgba(255,255,255,0.08);border-radius:10px;padding:12px;text-align:center;">
            <div style="font-size:10px;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:0.5px;">3 Yıldızlı Müdavim</div>
            <div style="font-size:22px;font-weight:800;color:#fbbf24;margin-top:2px;">{{ $threeStarsCount }}</div>
            <div style="font-size:10px;color:#64748b;">3+ kez gelen sadık kitle</div>
        </div>
    </div>

    {{-- 2. Kampanya & Şablon Yapılandırması --}}
    <div style="background:rgba(15,23,42,0.75);border:1px solid rgba(255,255,255,0.1);border-radius:12px;padding:16px;">
        <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:10px;margin-bottom:12px;">
            <div style="font-size:13px;font-weight:700;color:#f1f5f9;display:flex;align-items:center;gap:6px;">
                <span>🎯 Kampanya Mesaj Şablonu</span>
                <span style="font-size:11px;font-weight:500;color:#94a3b8;">(Ziyaretçiye özel değişkenler otomatik doldurulur)</span>
            </div>
            <div style="display:flex;align-items:center;gap:8px;">
                <label style="font-size:11px;color:#cbd5e1;font-weight:600;">Kupon Kodu:</label>
                <input
                    type="text"
                    x-model="couponCode"
                    style="background:rgba(30,41,59,0.9);border:1px solid rgba(255,255,255,0.2);border-radius:6px;padding:3px 8px;font-size:12px;font-weight:700;color:#fbbf24;text-transform:uppercase;width:100px;"
                />
            </div>
        </div>

        {{-- Şablon Hızlı Seçiciler --}}
        <div style="display:flex;flex-wrap:wrap;gap:6px;margin-bottom:10px;">
            <button
                type="button"
                @click="setTemplate('cart')"
                :style="activeTemplate === 'cart' ? 'background:rgba(244,63,94,0.2);border-color:#f43f5e;color:#fda4af;' : 'background:rgba(255,255,255,0.04);border-color:rgba(255,255,255,0.1);color:#94a3b8;'"
                style="border-width:1px;border-style:solid;border-radius:6px;padding:5px 10px;font-size:11px;font-weight:600;cursor:pointer;transition:all 0.15s;"
            >
                🛒 Sepet Tamamlama (%10)
            </button>
            <button
                type="button"
                @click="setTemplate('product')"
                :style="activeTemplate === 'product' ? 'background:rgba(56,189,248,0.2);border-color:#38bdf8;color:#7dd3fc;' : 'background:rgba(255,255,255,0.04);border-color:rgba(255,255,255,0.1);color:#94a3b8;'"
                style="border-width:1px;border-style:solid;border-radius:6px;padding:5px 10px;font-size:11px;font-weight:600;cursor:pointer;transition:all 0.15s;"
            >
                👟 Model & Beden Desteği
            </button>
            <button
                type="button"
                @click="setTemplate('vip')"
                :style="activeTemplate === 'vip' ? 'background:rgba(251,191,36,0.2);border-color:#fbbf24;color:#fde68a;' : 'background:rgba(255,255,255,0.04);border-color:rgba(255,255,255,0.1);color:#94a3b8;'"
                style="border-width:1px;border-style:solid;border-radius:6px;padding:5px 10px;font-size:11px;font-weight:600;cursor:pointer;transition:all 0.15s;"
            >
                ⭐ 3 Yıldız Müdavim (%15 VIP)
            </button>
            <button
                type="button"
                @click="setTemplate('shipping')"
                :style="activeTemplate === 'shipping' ? 'background:rgba(34,197,94,0.2);border-color:#22c55e;color:#86efac;' : 'background:rgba(255,255,255,0.04);border-color:rgba(255,255,255,0.1);color:#94a3b8;'"
                style="border-width:1px;border-style:solid;border-radius:6px;padding:5px 10px;font-size:11px;font-weight:600;cursor:pointer;transition:all 0.15s;"
            >
                🚀 Ücretsiz Hızlı Kargo Fırsatı
            </button>
        </div>

        {{-- Mesaj Düzenleme Textarea --}}
        <textarea
            x-model="customMessage"
            rows="3"
            style="width:100%;background:rgba(30,41,59,0.9);border:1px solid rgba(255,255,255,0.15);border-radius:8px;padding:10px 12px;font-size:12px;line-height:1.5;color:#f1f5f9;resize:vertical;font-family:inherit;"
            placeholder="Mesaj metnini buraya yazın..."
        ></textarea>

        {{-- Değişken İpuçları --}}
        <div style="display:flex;align-items:center;gap:6px;margin-top:6px;font-size:10.5px;color:#94a3b8;flex-wrap:wrap;">
            <span>Otomatik Değişkenler:</span>
            <code style="background:rgba(255,255,255,0.07);padding:1px 5px;border-radius:4px;color:#38bdf8;">{isim}</code>
            <code style="background:rgba(255,255,255,0.07);padding:1px 5px;border-radius:4px;color:#fbbf24;">{kupon}</code>
            <code style="background:rgba(255,255,255,0.07);padding:1px 5px;border-radius:4px;color:#34d399;">{site_url}</code>
            <span style="margin-left:auto;color:#64748b;">(Mesaj gönderilirken her müşterinin adına göre anlık yerleşir)</span>
        </div>
    </div>

    {{-- 3. Müşteri Listesi & Hızlı İletişim Butonları --}}
    <div style="background:rgba(15,23,42,0.75);border:1px solid rgba(255,255,255,0.1);border-radius:12px;padding:16px;">
        {{-- Filtre ve Arama Barı --}}
        <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:10px;margin-bottom:14px;">
            <div style="display:flex;flex-wrap:wrap;gap:6px;">
                <button
                    type="button"
                    @click="selectedFilter = 'all'"
                    :style="selectedFilter === 'all' ? 'background:#3b82f6;color:#ffffff;' : 'background:rgba(255,255,255,0.05);color:#94a3b8;'"
                    style="border:none;border-radius:6px;padding:4px 10px;font-size:11px;font-weight:700;cursor:pointer;"
                >
                    Tümü (<span x-text="visitors.length"></span>)
                </button>
                <button
                    type="button"
                    @click="selectedFilter = 'cart'"
                    :style="selectedFilter === 'cart' ? 'background:#f43f5e;color:#ffffff;' : 'background:rgba(255,255,255,0.05);color:#94a3b8;'"
                    style="border:none;border-radius:6px;padding:4px 10px;font-size:11px;font-weight:700;cursor:pointer;"
                >
                    🛒 Sepeti Dolu ({{ $cartCount }})
                </button>
                <button
                    type="button"
                    @click="selectedFilter = 'three_stars'"
                    :style="selectedFilter === 'three_stars' ? 'background:#fbbf24;color:#0f172a;' : 'background:rgba(255,255,255,0.05);color:#94a3b8;'"
                    style="border:none;border-radius:6px;padding:4px 10px;font-size:11px;font-weight:700;cursor:pointer;"
                >
                    ⭐ 3 Yıldız ({{ $threeStarsCount }})
                </button>
                <button
                    type="button"
                    @click="selectedFilter = 'high_intent'"
                    :style="selectedFilter === 'high_intent' ? 'background:#10b981;color:#ffffff;' : 'background:rgba(255,255,255,0.05);color:#94a3b8;'"
                    style="border:none;border-radius:6px;padding:4px 10px;font-size:11px;font-weight:700;cursor:pointer;"
                >
                    🔥 Sıcak Aday (%60+)
                </button>
            </div>

            <div style="position:relative;width:200px;">
                <input
                    type="text"
                    x-model="searchTerm"
                    placeholder="Müşteri veya tel ara..."
                    style="width:100%;background:rgba(30,41,59,0.8);border:1px solid rgba(255,255,255,0.15);border-radius:6px;padding:4px 8px;font-size:11px;color:#f8fafc;"
                />
            </div>
        </div>

        {{-- Müşteriler Listesi (Scrollable) --}}
        <div style="max-height:340px;overflow-y:auto;display:flex;flex-direction:column;gap:8px;padding-right:4px;">
            <template x-for="visitor in filteredVisitors" :key="visitor.id">
                <div style="display:flex;align-items:center;justify-content:space-between;gap:12px;background:rgba(30,41,59,0.6);border:1px solid rgba(255,255,255,0.07);border-radius:8px;padding:10px 14px;transition:all 0.15s;" onmouseover="this.style.borderColor='rgba(34,197,94,0.4)'" onmouseout="this.style.borderColor='rgba(255,255,255,0.07)'">
                    {{-- Sol: Müşteri Künyesi --}}
                    <div style="display:flex;align-items:center;gap:10px;min-width:0;flex:1;">
                        <div style="width:34px;height:34px;border-radius:50%;background:#0f172a;border:1px solid rgba(255,255,255,0.1);display:flex;align-items:center;justify-content:center;font-weight:700;color:#f1f5f9;font-size:12px;flex-shrink:0;">
                            <span x-text="visitor.name ? visitor.name.charAt(0) : 'M'"></span>
                        </div>
                        <div style="overflow:hidden;flex:1;">
                            <div style="display:flex;align-items:center;gap:6px;flex-wrap:wrap;">
                                <strong style="font-size:12.5px;color:#f8fafc;" x-text="visitor.name"></strong>
                                <span style="font-size:10px;color:#fbbf24;" x-text="'⭐'.repeat(Math.min(3, visitor.stars))"></span>
                                <span style="font-size:9.5px;padding:1px 5px;border-radius:4px;background:rgba(255,255,255,0.06);color:#94a3b8;font-weight:600;" x-text="visitor.visit_count + '. Gelişi'"></span>
                                <span x-show="visitor.is_member" style="font-size:9px;padding:1px 5px;border-radius:4px;background:rgba(255,78,0,0.15);color:#ff7849;font-weight:700;">ÜYE</span>
                            </div>
                            <div style="display:flex;align-items:center;gap:8px;font-size:11px;color:#cbd5e1;margin-top:2px;flex-wrap:wrap;">
                                <span style="color:#38bdf8;font-weight:600;">📱 <span x-text="visitor.phone"></span></span>
                                <span x-show="visitor.cart_items > 0" style="color:#f43f5e;font-weight:600;">
                                    • 🛒 <span x-text="visitor.cart_items"></span> ürün (<span x-text="visitor.cart_total_formatted"></span>)
                                </span>
                                <span x-show="visitor.cart_items <= 0 && visitor.product_name" style="color:#a78bfa;font-size:10px;">
                                    • İnceledi: <span x-text="visitor.product_name"></span>
                                </span>
                            </div>
                        </div>
                    </div>

                    {{-- Sağ: Tek Tıkla WhatsApp Aç Butonu --}}
                    <div style="flex-shrink:0;">
                        <a
                            :href="getWhatsAppUrl(visitor.clean_phone, visitor.name)"
                            target="_blank"
                            title="Bu müşteriye özel WhatsApp sohbetini başlat"
                            style="display:inline-flex;align-items:center;gap:5px;background:#22c55e;color:#ffffff;padding:6px 12px;border-radius:7px;font-size:11.5px;font-weight:700;text-decoration:none;box-shadow:0 2px 6px rgba(34,197,94,0.3);transition:all 0.15s ease;"
                            onmouseover="this.style.background='#16a34a';this.style.transform='scale(1.02)';"
                            onmouseout="this.style.background='#22c55e';this.style.transform='scale(1)';"
                        >
                            <svg style="width:13px;height:13px;fill:currentColor;" viewBox="0 0 24 24">
                                <path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/>
                            </svg>
                            <span>WhatsApp Başlat ↗</span>
                        </a>
                    </div>
                </div>
            </template>

            <div x-show="filteredVisitors.length === 0" style="text-align:center;padding:30px;color:#94a3b8;font-size:12px;">
                Bu filtre kriterine uyan telefonlu müşteri bulunamadı.
            </div>
        </div>
    </div>

    {{-- 4. Toplu Dışa Aktarma & Kopyalama Araçları --}}
    <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:10px;background:rgba(30,41,59,0.5);border:1px solid rgba(255,255,255,0.08);border-radius:10px;padding:12px 16px;">
        <div style="font-size:11px;color:#94a3b8;">
            <span>Toplu Araçlar (WhatsApp Business / Toplu SMS için):</span>
        </div>
        <div style="display:flex;align-items:center;gap:8px;flex-wrap:wrap;">
            <button
                type="button"
                @click="copyPhoneList()"
                style="background:rgba(255,255,255,0.06);border:1px solid rgba(255,255,255,0.15);border-radius:6px;padding:5px 11px;font-size:11px;font-weight:600;color:#f8fafc;cursor:pointer;"
            >
                <span x-text="copiedType === 'phones' ? '✅ Numaralar Kopyalandı!' : '📋 Numaraları Kopyala'"></span>
            </button>

            <button
                type="button"
                @click="copyDetailedList()"
                style="background:rgba(255,255,255,0.06);border:1px solid rgba(255,255,255,0.15);border-radius:6px;padding:5px 11px;font-size:11px;font-weight:600;color:#f8fafc;cursor:pointer;"
            >
                <span x-text="copiedType === 'detailed' ? '✅ Excel Listesi Kopyalandı!' : '📋 İsim & Tel Listesi (Excel)'"></span>
            </button>

            <button
                type="button"
                @click="copyMessage()"
                style="background:rgba(255,255,255,0.06);border:1px solid rgba(255,255,255,0.15);border-radius:6px;padding:5px 11px;font-size:11px;font-weight:600;color:#f8fafc;cursor:pointer;"
            >
                <span x-text="copiedType === 'message' ? '✅ Mesaj Kopyalandı!' : '📋 Mesaj Metnini Kopyala'"></span>
            </button>
        </div>
    </div>
</div>
