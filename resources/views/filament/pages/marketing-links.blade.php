<x-filament-panels::page>
    <div 
        x-data="{
            copyToast: false,
            toastMsg: '',
            baseUrl: '{{ $baseUrl }}',
            selectedTarget: 'home',
            customPath: '',
            selectedProductSlug: '{{ $products->first()?->slug ?? '' }}',
            selectedChannel: 'ig_story',
            campaignName: '',
            get generatedUrl() {
                let targetUrl = this.baseUrl;
                if (this.selectedTarget === 'product' && this.selectedProductSlug) {
                    targetUrl += '/urun/' + this.selectedProductSlug;
                } else if (this.selectedTarget === 'custom' && this.customPath) {
                    let path = this.customPath.trim();
                    if (!path.startsWith('http')) {
                        if (!path.startsWith('/')) path = '/' + path;
                        targetUrl += path;
                    } else {
                        targetUrl = path;
                    }
                }

                let params = [];
                let cleanCamp = this.campaignName.trim().replace(/\s+/g, '_').toLowerCase();

                switch(this.selectedChannel) {
                    case 'ig_bio':
                        params.push('utm_source=instagram', 'utm_medium=bio');
                        if (cleanCamp) params.push('utm_campaign=' + cleanCamp);
                        break;
                    case 'ig_story':
                        params.push('utm_source=instagram', 'utm_medium=story');
                        params.push('utm_campaign=' + (cleanCamp || 'story_paylasim'));
                        break;
                    case 'ig_reels':
                        params.push('utm_source=instagram', 'utm_medium=reels');
                        if (cleanCamp) params.push('utm_campaign=' + cleanCamp);
                        break;
                    case 'meta_ads':
                        params.push('utm_source=instagram', 'utm_medium=cpc');
                        params.push('utm_campaign=' + (cleanCamp || '{{campaign.name}}'));
                        break;
                    case 'tiktok_bio':
                        params.push('utm_source=tiktok', 'utm_medium=bio');
                        if (cleanCamp) params.push('utm_campaign=' + cleanCamp);
                        break;
                    case 'tiktok_video':
                        params.push('utm_source=tiktok', 'utm_medium=video');
                        params.push('utm_campaign=' + (cleanCamp || 'kesfet'));
                        break;
                    case 'google_ads':
                        params.push('utm_source=google', 'utm_medium=cpc');
                        params.push('utm_campaign=' + (cleanCamp || '{{campaignid}}'));
                        break;
                    case 'whatsapp':
                        params.push('utm_source=whatsapp', 'utm_medium=chat');
                        if (cleanCamp) params.push('utm_campaign=' + cleanCamp);
                        break;
                    case 'sms':
                        params.push('utm_source=sms', 'utm_medium=bulk_sms');
                        params.push('utm_campaign=' + (cleanCamp || 'firsat'));
                        break;
                    case 'influencer':
                        params.push('utm_source=influencer', 'utm_medium=story');
                        params.push('utm_campaign=' + (cleanCamp || 'isbirligi'));
                        break;
                }

                if (params.length > 0) {
                    let separator = targetUrl.includes('?') ? '&' : '?';
                    return targetUrl + separator + params.join('&');
                }
                return targetUrl;
            },
            copyText(text, label) {
                navigator.clipboard.writeText(text);
                this.toastMsg = (label || 'Link') + ' panoya kopyalandı!';
                this.copyToast = true;
                setTimeout(() => this.copyToast = false, 2500);
            }
        }"
        class="space-y-6"
    >
        <!-- Floating Toast Bildirimi -->
        <div 
            x-show="copyToast" 
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 translate-y-2 scale-95"
            x-transition:enter-end="opacity-100 translate-y-0 scale-100"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100 translate-y-0 scale-100"
            x-transition:leave-end="opacity-0 translate-y-2 scale-95"
            style="position: fixed; bottom: 28px; right: 28px; z-index: 999999; background: #10b981; color: #ffffff; padding: 12px 22px; border-radius: 12px; font-size: 0.88rem; font-weight: 700; display: flex; align-items: center; gap: 10px; box-shadow: 0 12px 35px rgba(0,0,0,0.35); pointer-events: none;"
            x-cloak
        >
            <svg style="width:20px; height:20px; flex-shrink:0;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
            <span x-text="toastMsg"></span>
        </div>

        <!-- Bilgi Banner -->
        <div class="p-4 rounded-xl border border-blue-200 bg-blue-50 dark:border-blue-900/40 dark:bg-blue-950/20 text-blue-900 dark:text-blue-300 flex items-start gap-3">
            <svg class="w-6 h-6 text-blue-600 dark:text-blue-400 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <div class="text-sm">
                <strong>Bu linkler ne işe yarar?</strong> Aşağıdaki linkleri Instagram profilinizde, hikayelerinizde, Reels videolarınızda veya reklamlarınızda kullandığınızda; tıklayıp gelen müşteriler sipariş verdiğinde sistemimiz otomatik olarak kaynağı tespit eder ve <strong>Siparişler</strong> sayfasında <em>(📸 Instagram, 📱 Mobil, Kampanya Adı)</em> şeklinde rozetlerle gösterir.
            </div>
        </div>

        <!-- 1. INSTAGRAM HIZLI KOPYALAMA KARTLARI -->
        <div class="bg-white dark:bg-gray-900 rounded-2xl border border-gray-200 dark:border-gray-800 p-6 shadow-sm">
            <div class="flex items-center justify-between border-b border-gray-100 dark:border-gray-800 pb-4 mb-5">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-amber-500 via-pink-500 to-purple-600 flex items-center justify-center text-white shadow-md">
                        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.13-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/></svg>
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-gray-900 dark:text-white">Instagram İçin Hazır Linkler</h3>
                        <p class="text-xs text-gray-500">Instagram hesabınızda doğrudan kullanabileceğiniz hazır bağlantılar</p>
                    </div>
                </div>
                <span class="text-xs font-semibold px-2.5 py-1 rounded-full bg-pink-100 text-pink-700 dark:bg-pink-900/30 dark:text-pink-300">En Çok Kullanılan</span>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- 1. Bio Linki -->
                <div class="p-4 rounded-xl border border-gray-100 dark:border-gray-800 bg-gray-50/50 dark:bg-gray-800/30 flex flex-col justify-between gap-3">
                    <div>
                        <div class="flex items-center justify-between mb-1">
                            <span class="font-bold text-sm text-gray-900 dark:text-white flex items-center gap-1.5">
                                <span>📌</span> Instagram Profil Biyografisi (Bio)
                            </span>
                            <span class="text-[11px] px-2 py-0.5 rounded bg-gray-200 dark:bg-gray-700 text-gray-600 dark:text-gray-300 font-medium">Profil</span>
                        </div>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mb-2">Profildeki "Web Sitesi" alanına yapıştırılacak ana link.</p>
                        <div class="bg-white dark:bg-gray-950 p-2.5 rounded-lg border border-gray-200 dark:border-gray-800 font-mono text-xs text-gray-700 dark:text-gray-300 break-all select-all">
                            {{ $baseUrl }}/?utm_source=instagram&utm_medium=bio
                        </div>
                    </div>
                    <button 
                        type="button"
                        @click="copyText('{{ $baseUrl }}/?utm_source=instagram&utm_medium=bio', 'Instagram Bio Linki')"
                        class="w-full inline-flex items-center justify-center gap-2 px-3 py-2 text-xs font-semibold rounded-lg bg-pink-600 hover:bg-pink-700 text-white transition-all shadow-sm active:scale-95 cursor-pointer"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                        <span>Bio Linkini Kopyala</span>
                    </button>
                </div>

                <!-- 2. Story Linki -->
                <div class="p-4 rounded-xl border border-gray-100 dark:border-gray-800 bg-gray-50/50 dark:bg-gray-800/30 flex flex-col justify-between gap-3">
                    <div>
                        <div class="flex items-center justify-between mb-1">
                            <span class="font-bold text-sm text-gray-900 dark:text-white flex items-center gap-1.5">
                                <span>✨</span> Instagram Hikaye (Story) Linki
                            </span>
                            <span class="text-[11px] px-2 py-0.5 rounded bg-gray-200 dark:bg-gray-700 text-gray-600 dark:text-gray-300 font-medium">Hikaye</span>
                        </div>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mb-2">Hikayelere ekleyeceğiniz bağlantı çıkartmasında kullanılır.</p>
                        <div class="bg-white dark:bg-gray-950 p-2.5 rounded-lg border border-gray-200 dark:border-gray-800 font-mono text-xs text-gray-700 dark:text-gray-300 break-all select-all">
                            {{ $baseUrl }}/?utm_source=instagram&utm_medium=story&utm_campaign=story_paylasim
                        </div>
                    </div>
                    <button 
                        type="button"
                        @click="copyText('{{ $baseUrl }}/?utm_source=instagram&utm_medium=story&utm_campaign=story_paylasim', 'Instagram Story Linki')"
                        class="w-full inline-flex items-center justify-center gap-2 px-3 py-2 text-xs font-semibold rounded-lg bg-pink-600 hover:bg-pink-700 text-white transition-all shadow-sm active:scale-95 cursor-pointer"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                        <span>Story Linkini Kopyala</span>
                    </button>
                </div>

                <!-- 3. Reels Linki -->
                <div class="p-4 rounded-xl border border-gray-100 dark:border-gray-800 bg-gray-50/50 dark:bg-gray-800/30 flex flex-col justify-between gap-3">
                    <div>
                        <div class="flex items-center justify-between mb-1">
                            <span class="font-bold text-sm text-gray-900 dark:text-white flex items-center gap-1.5">
                                <span>🎬</span> Instagram Reels Paylaşımı
                            </span>
                            <span class="text-[11px] px-2 py-0.5 rounded bg-gray-200 dark:bg-gray-700 text-gray-600 dark:text-gray-300 font-medium">Reels</span>
                        </div>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mb-2">Reels açıklamalarında veya DM yönlendirmelerinde kullanılır.</p>
                        <div class="bg-white dark:bg-gray-950 p-2.5 rounded-lg border border-gray-200 dark:border-gray-800 font-mono text-xs text-gray-700 dark:text-gray-300 break-all select-all">
                            {{ $baseUrl }}/?utm_source=instagram&utm_medium=reels
                        </div>
                    </div>
                    <button 
                        type="button"
                        @click="copyText('{{ $baseUrl }}/?utm_source=instagram&utm_medium=reels', 'Instagram Reels Linki')"
                        class="w-full inline-flex items-center justify-center gap-2 px-3 py-2 text-xs font-semibold rounded-lg bg-pink-600 hover:bg-pink-700 text-white transition-all shadow-sm active:scale-95 cursor-pointer"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                        <span>Reels Linkini Kopyala</span>
                    </button>
                </div>

                <!-- 4. Meta / Instagram Reklam Parametresi -->
                <div class="p-4 rounded-xl border border-gray-100 dark:border-gray-800 bg-gray-50/50 dark:bg-gray-800/30 flex flex-col justify-between gap-3">
                    <div>
                        <div class="flex items-center justify-between mb-1">
                            <span class="font-bold text-sm text-gray-900 dark:text-white flex items-center gap-1.5">
                                <span>🎯</span> Meta (Instagram / Facebook) Reklam Parametresi
                            </span>
                            <span class="text-[11px] px-2 py-0.5 rounded bg-purple-100 dark:bg-purple-900/30 text-purple-700 dark:text-purple-300 font-medium">Meta Ads</span>
                        </div>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mb-2">Meta Reklam Yöneticisi'nde "URL Parametreleri" kutusuna yapıştırın:</p>
                        <div class="bg-white dark:bg-gray-950 p-2.5 rounded-lg border border-gray-200 dark:border-gray-800 font-mono text-xs text-purple-600 dark:text-purple-400 break-all select-all">
                            utm_source=instagram&utm_medium=cpc&utm_campaign=@{{campaign.name}}
                        </div>
                    </div>
                    <button 
                        type="button"
                        @click="copyText('utm_source=instagram&utm_medium=cpc&utm_campaign=\{\{campaign.name\}\}', 'Meta Reklam Parametresi')"
                        class="w-full inline-flex items-center justify-center gap-2 px-3 py-2 text-xs font-semibold rounded-lg bg-purple-600 hover:bg-purple-700 text-white transition-all shadow-sm active:scale-95 cursor-pointer"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                        <span>Reklam Parametresini Kopyala</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- 2. ÖZEL ÜRÜN & KAMPANYA LINK OLUŞTURUCU (İNTERAKTİF) -->
        <div class="bg-white dark:bg-gray-900 rounded-2xl border border-gray-200 dark:border-gray-800 p-6 shadow-sm">
            <div class="flex items-center gap-3 border-b border-gray-100 dark:border-gray-800 pb-4 mb-5">
                <div class="w-10 h-10 rounded-xl bg-blue-600 flex items-center justify-center text-white shadow-md">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"/></svg>
                </div>
                <div>
                    <h3 class="text-base font-bold text-gray-900 dark:text-white">Özel Ürün & Kampanya Link Oluşturucu</h3>
                    <p class="text-xs text-gray-500">İstediğiniz ürünü ve paylaşım kanalını seçin, takip linkiniz anında oluşturulsun</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-5 mb-5">
                <!-- 1. Hedef Sayfa / Ürün -->
                <div>
                    <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider mb-2">1. Hedef Sayfa</label>
                    <select 
                        x-model="selectedTarget" 
                        class="w-full text-sm rounded-xl border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-blue-500 focus:border-blue-500 p-2.5"
                    >
                        <option value="home">Ana Sayfa (/)</option>
                        <option value="product">Belirli Bir Ürün Sayfası</option>
                        <option value="custom">Özel Link / Yol Yaz</option>
                    </select>

                    <!-- Ürün Seçimi -->
                    <template x-if="selectedTarget === 'product'">
                        <div class="mt-3">
                            <label class="block text-xs text-gray-500 mb-1">Ürünü Seçin:</label>
                            <select 
                                x-model="selectedProductSlug"
                                class="w-full text-sm rounded-xl border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-blue-500 focus:border-blue-500 p-2.5"
                            >
                                @foreach($products as $product)
                                    <option value="{{ $product->slug }}">{{ $product->name }} (₺{{ number_format($product->price, 0) }})</option>
                                @endforeach
                            </select>
                        </div>
                    </template>

                    <!-- Özel Link -->
                    <template x-if="selectedTarget === 'custom'">
                        <div class="mt-3">
                            <label class="block text-xs text-gray-500 mb-1">Özel URL / Yol:</label>
                            <input 
                                type="text" 
                                x-model="customPath" 
                                placeholder="/kategori/patenler veya tam link" 
                                class="w-full text-sm rounded-xl border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-blue-500 focus:border-blue-500 p-2.5"
                            />
                        </div>
                    </template>
                </div>

                <!-- 2. Paylaşım Kanalı / Mecra -->
                <div>
                    <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider mb-2">2. Paylaşım Kanalı</label>
                    <select 
                        x-model="selectedChannel" 
                        class="w-full text-sm rounded-xl border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-blue-500 focus:border-blue-500 p-2.5"
                    >
                        <optgroup label="Instagram">
                            <option value="ig_story">📸 Instagram Hikaye (Story)</option>
                            <option value="ig_bio">📌 Instagram Profil Biyografisi (Bio)</option>
                            <option value="ig_reels">🎬 Instagram Reels</option>
                            <option value="meta_ads">🎯 Meta / Instagram Ücretli Reklam</option>
                        </optgroup>
                        <optgroup label="Diğer Sosyal Medya">
                            <option value="tiktok_bio">🎵 TikTok Profil Linki</option>
                            <option value="tiktok_video">🎵 TikTok Video / Reklam</option>
                            <option value="google_ads">🔍 Google Ads Reklamı</option>
                        </optgroup>
                        <optgroup label="Doğrudan İletişim">
                            <option value="whatsapp">💬 WhatsApp Mesaj / Destek</option>
                            <option value="sms">✉️ Toplu SMS Kampanyası</option>
                            <option value="influencer">🤝 Influencer / İş Birliği</option>
                        </optgroup>
                    </select>
                    <p class="text-[11px] text-gray-400 mt-2">Kanal seçildiğinde kaynak etiketi otomatik ayarlanır.</p>
                </div>

                <!-- 3. Kampanya Adı (Opsiyonel) -->
                <div>
                    <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider mb-2">3. Kampanya Adı (Opsiyonel)</label>
                    <input 
                        type="text" 
                        x-model="campaignName" 
                        placeholder="Örn: eylul_indirimi, okuladonus, reels1" 
                        class="w-full text-sm rounded-xl border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-blue-500 focus:border-blue-500 p-2.5"
                    />
                    <p class="text-[11px] text-gray-400 mt-2">Sipariş geldiğinde hangi kampanyanızdan satıldığını görmek için isim verin.</p>
                </div>
            </div>

            <!-- Canlı Üretilen Link Gösterimi -->
            <div class="mt-6 p-4 rounded-xl border border-blue-100 dark:border-blue-900/40 bg-blue-50/40 dark:bg-blue-950/20">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-xs font-bold text-blue-900 dark:text-blue-300 uppercase tracking-wider flex items-center gap-1.5">
                        <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/></svg>
                        Üretilen Takip Bağlantısı
                    </span>
                    <span class="text-xs text-blue-600 dark:text-blue-400 font-medium">Kullanıma Hazır</span>
                </div>

                <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3">
                    <div 
                        x-text="generatedUrl" 
                        class="flex-1 p-3 rounded-xl bg-white dark:bg-gray-950 border border-blue-200 dark:border-blue-900/60 font-mono text-xs text-blue-950 dark:text-blue-200 break-all select-all shadow-inner"
                    ></div>
                    <button 
                        type="button" 
                        @click="copyText(generatedUrl, 'Özel Takip Linki')" 
                        class="px-5 py-3 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs inline-flex items-center justify-center gap-2 shadow-md transition-all active:scale-95 cursor-pointer whitespace-nowrap"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                        <span>Linki Kopyala</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- 3. DİĞER KANALLAR (TIKTOK, WHATSAPP, SMS, GOOGLE) -->
        <div class="bg-white dark:bg-gray-900 rounded-2xl border border-gray-200 dark:border-gray-800 p-6 shadow-sm">
            <h3 class="text-base font-bold text-gray-900 dark:text-white border-b border-gray-100 dark:border-gray-800 pb-3 mb-4">
                Diğer Pazarlama Kanalları
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <!-- TikTok -->
                <div class="p-3.5 rounded-xl border border-gray-100 dark:border-gray-800 bg-gray-50/50 dark:bg-gray-800/30 flex flex-col justify-between gap-2.5">
                    <div>
                        <div class="font-bold text-xs text-gray-900 dark:text-white flex items-center gap-1.5 mb-1">
                            <span>🎵</span> TikTok Biyografi / Profil
                        </div>
                        <div class="bg-white dark:bg-gray-950 p-2 rounded border border-gray-200 dark:border-gray-800 font-mono text-[11px] text-gray-600 dark:text-gray-300 break-all select-all">
                            {{ $baseUrl }}/?utm_source=tiktok&utm_medium=bio
                        </div>
                    </div>
                    <button 
                        type="button" 
                        @click="copyText('{{ $baseUrl }}/?utm_source=tiktok&utm_medium=bio', 'TikTok Linki')" 
                        class="w-full py-1.5 px-3 rounded bg-gray-900 hover:bg-black text-white text-xs font-semibold cursor-pointer"
                    >
                        Kopyala
                    </button>
                </div>

                <!-- WhatsApp -->
                <div class="p-3.5 rounded-xl border border-gray-100 dark:border-gray-800 bg-gray-50/50 dark:bg-gray-800/30 flex flex-col justify-between gap-2.5">
                    <div>
                        <div class="font-bold text-xs text-gray-900 dark:text-white flex items-center gap-1.5 mb-1">
                            <span>💬</span> WhatsApp Müşteri Linki
                        </div>
                        <div class="bg-white dark:bg-gray-950 p-2 rounded border border-gray-200 dark:border-gray-800 font-mono text-[11px] text-gray-600 dark:text-gray-300 break-all select-all">
                            {{ $baseUrl }}/?utm_source=whatsapp&utm_medium=chat
                        </div>
                    </div>
                    <button 
                        type="button" 
                        @click="copyText('{{ $baseUrl }}/?utm_source=whatsapp&utm_medium=chat', 'WhatsApp Linki')" 
                        class="w-full py-1.5 px-3 rounded bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold cursor-pointer"
                    >
                        Kopyala
                    </button>
                </div>

                <!-- Toplu SMS -->
                <div class="p-3.5 rounded-xl border border-gray-100 dark:border-gray-800 bg-gray-50/50 dark:bg-gray-800/30 flex flex-col justify-between gap-2.5">
                    <div>
                        <div class="font-bold text-xs text-gray-900 dark:text-white flex items-center gap-1.5 mb-1">
                            <span>✉️</span> Toplu SMS Kampanyası
                        </div>
                        <div class="bg-white dark:bg-gray-950 p-2 rounded border border-gray-200 dark:border-gray-800 font-mono text-[11px] text-gray-600 dark:text-gray-300 break-all select-all">
                            {{ $baseUrl }}/?utm_source=sms&utm_medium=bulk_sms&utm_campaign=firsat
                        </div>
                    </div>
                    <button 
                        type="button" 
                        @click="copyText('{{ $baseUrl }}/?utm_source=sms&utm_medium=bulk_sms&utm_campaign=firsat', 'SMS Linki')" 
                        class="w-full py-1.5 px-3 rounded bg-amber-600 hover:bg-amber-700 text-white text-xs font-semibold cursor-pointer"
                    >
                        Kopyala
                    </button>
                </div>

                <!-- Google Ads -->
                <div class="p-3.5 rounded-xl border border-gray-100 dark:border-gray-800 bg-gray-50/50 dark:bg-gray-800/30 flex flex-col justify-between gap-2.5">
                    <div>
                        <div class="font-bold text-xs text-gray-900 dark:text-white flex items-center gap-1.5 mb-1">
                            <span>🔍</span> Google Ads URL Son Eki
                        </div>
                        <div class="bg-white dark:bg-gray-950 p-2 rounded border border-gray-200 dark:border-gray-800 font-mono text-[11px] text-gray-600 dark:text-gray-300 break-all select-all">
                            utm_source=google&utm_medium=cpc&utm_campaign=@{{campaignid}}
                        </div>
                    </div>
                    <button 
                        type="button" 
                        @click="copyText('utm_source=google&utm_medium=cpc&utm_campaign=\{\{campaignid\}\}', 'Google Ads Parametresi')" 
                        class="w-full py-1.5 px-3 rounded bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold cursor-pointer"
                    >
                        Kopyala
                    </button>
                </div>
            </div>
        </div>
    </div>
</x-filament-panels::page>
