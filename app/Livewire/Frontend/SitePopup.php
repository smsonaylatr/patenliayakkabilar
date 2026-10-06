<?php

namespace App\Livewire\Frontend;

use Livewire\Component;
use App\Models\Setting;
use Illuminate\Support\Facades\Storage;

class SitePopup extends Component
{
    public bool $isActive = false;
    public ?string $imageUrl = null;
    public ?string $linkUrl = null;

    public function mount()
    {
        $this->loadSettings();
    }

    public function render()
    {
        // Her render'da DB'deki güncel ayarları yükle, client manipulation'ı engelle
        $this->loadSettings();

        return view('livewire.frontend.site-popup', [
            'link' => is_string($this->linkUrl) ? $this->linkUrl : null,
            'image' => is_string($this->imageUrl) ? $this->imageUrl : null,
        ]);
    }

    /**
     * İstemciden gelebilecek kural dışı güncellemeleri sessizce yoksay
     */
    public function updating($property, $value)
    {
        // SitePopup durumları yalnızca sunucu tarafı ayarlarından okunur
    }

    protected function loadSettings(): void
    {
        $settings = Setting::whereIn('key', ['popup_active', 'popup_image', 'popup_link'])
            ->pluck('value', 'key')
            ->toArray();

        $this->isActive = isset($settings['popup_active']) && $settings['popup_active'] == '1';

        $rawImage = $settings['popup_image'] ?? null;
        if (is_array($rawImage)) {
            $rawImage = reset($rawImage);
        }

        $rawLink = $settings['popup_link'] ?? null;
        if (is_array($rawLink)) {
            $rawLink = reset($rawLink);
        }

        if ($this->isActive && !empty($rawImage)) {
            $this->imageUrl = Storage::disk('public')->url($rawImage);
            $this->linkUrl = is_string($rawLink) ? $rawLink : (is_scalar($rawLink) ? (string)$rawLink : null);
        } else {
            $this->isActive = false;
            $this->imageUrl = null;
            $this->linkUrl = null;
        }
    }
}
