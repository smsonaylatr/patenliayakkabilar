<?php

namespace App\Filament\Resources\Products\Pages;

use App\Filament\Resources\Products\ProductResource;
use App\Models\Product;
use App\Services\ProductAiExportService;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\HtmlString;

class ListProducts extends ListRecords
{
    protected static string $resource = ProductResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('aiExportCatalog')
                ->label('AI Katalog İndir (Tüm Ürünler)')
                ->icon('heroicon-o-sparkles')
                ->color('success')
                ->modalHeading('Tüm Kataloğu Yapay Zeka İçin Dışa Aktar')
                ->modalDescription('Katalogdaki tüm aktif veya filtrelenmiş ürünleri ChatGPT, Claude, Gemini veya e-tablo analizlerinde kullanmak üzere tek tıkla Excel, CSV veya PDF olarak indirebilirsiniz.')
                ->modalSubmitActionLabel('Kataloğu İndir')
                ->form([
                    Select::make('format')
                        ->label('Dışa Aktarma Formatı')
                        ->options([
                            'xlsx' => '📊 Excel Çalışma Kitabı (.xlsx) — Çoklu Sayfa (Katalog + Bedenler + AI Rehberi)',
                            'csv'  => '📄 CSV Dosyası (.csv) — Saf Metin Tablo (ChatGPT / Claude Uyumlu)',
                            'pdf'  => '📑 PDF Dokümanı (.pdf) — Yapay Zeka Katalog Raporu',
                        ])
                        ->default('xlsx')
                        ->required()
                        ->native(false),
                    Select::make('status_filter')
                        ->label('Hangi Ürünler Aktarılsın?')
                        ->options([
                            'active' => 'Yalnızca Aktif Satışta Olan Ürünler',
                            'all'    => 'Tüm Ürünler (Aktif ve Pasif)',
                        ])
                        ->default('active')
                        ->native(false),
                    Toggle::make('include_descriptions')
                        ->label('Detaylı ürün açıklamalarını dahil et')
                        ->default(true)
                        ->helperText('HTML etiketlerinden arındırılmış temiz metin olarak aktarılır.'),
                    Toggle::make('only_in_stock')
                        ->label('Yalnızca stokta olan varyantları dahil et')
                        ->default(false)
                        ->helperText('Aktif edilirse tükenmiş beden varyantları listeye eklenmez.'),
                ])
                ->action(function (array $data) {
                    $query = Product::query();
                    if (($data['status_filter'] ?? 'active') === 'active') {
                        $query->where('status', true);
                    }

                    $format = $data['format'] ?? 'xlsx';
                    $service = app(ProductAiExportService::class);

                    return $service->export($query, $format, [
                        'include_descriptions' => (bool) ($data['include_descriptions'] ?? true),
                        'only_in_stock' => (bool) ($data['only_in_stock'] ?? false),
                    ]);
                }),
            Action::make('syncPoregoStock')
                ->label('Porego Stok Entegrasyonu')
                ->icon('heroicon-o-arrow-path')
                ->color('warning')
                ->modalHeading('Porego / Paketfy Stok & Ürün Entegrasyonu')
                ->modalDescription(new HtmlString('
                    <div style="font-size: 14px; line-height: 1.6; color: #cbd5e1;">
                        <p style="margin-bottom: 12px;">✅ <b>Sitenizdeki Porego Canlı API Beslemesi Aktiftir.</b> Sitedeki tüm aktif ürünler, beden varyantları, SKU kodları ve stok miktarları otomatik olarak sunulmaktadır.</p>
                        
                        <div style="background: rgba(15, 23, 42, 0.6); border: 1px solid rgba(255, 255, 255, 0.1); border-radius: 8px; padding: 12px; margin-bottom: 12px;">
                            <div style="margin-bottom: 6px;"><b>📌 Ürün Çekim URL (Paketfy / Porego için):</b></div>
                            <code style="color: #38bdf8; word-break: break-all;">https://patenliayakkabilar.com/api/porego/products</code>
                            <div style="margin-top: 8px; margin-bottom: 6px;"><b>📌 Stok Çekim URL:</b></div>
                            <code style="color: #38bdf8; word-break: break-all;">https://patenliayakkabilar.com/api/porego/stock</code>
                        </div>

                        <p style="margin-bottom: 8px;">👉 <b>Porego Panelinde Ürünleri Görmek İçin:</b></p>
                        <ol style="margin-left: 20px; list-style-type: decimal;">
                            <li><a href="https://app.porego.com/dashboard/products" target="_blank" style="color: #38bdf8; text-decoration: underline;">app.porego.com/dashboard/products</a> adresine gidin.</li>
                            <li>Sayfadaki <b>"Mağazanızı Senkronize Edin"</b> butonuna tıklayın.</li>
                        </ol>
                        <p style="margin-top: 10px; font-size: 12px; color: #94a3b8;">Aşağıdaki butonla ayrıca doğrudan Porego API\'sine de push denemesi yapabilirsiniz.</p>
                    </div>
                '))
                ->modalSubmitActionLabel('Porego API Push Denemesi Yap')
                ->modalCancelActionLabel('Kapat')
                ->action(function (): void {
                    $result = app(\App\Services\PoregoApiService::class)->syncProducts();
                    if ($result['success']) {
                        \Filament\Notifications\Notification::make()
                            ->title('Porego Stok Senkronizasyonu Başarılı')
                            ->body($result['message'])
                            ->success()
                            ->send();
                    } else {
                        \Filament\Notifications\Notification::make()
                            ->title('Porego Canlı API Beslemesi Hazır')
                            ->body("Sitedeki API beslemesi aktiftir.\n\nPorego (Paketfy) panelinden ('app.porego.com/dashboard/products') 'Mağazanızı Senkronize Edin' butonuna basarak tüm ürünleri çekebilirsiniz.")
                            ->info()
                            ->persistent()
                            ->send();
                    }
                }),
            CreateAction::make()->label('Yeni Ürün'),
        ];
    }
}
