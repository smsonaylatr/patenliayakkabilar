<?php

namespace App\Livewire\Frontend;

use Livewire\Component;
use App\Models\Setting;
use Illuminate\Support\Facades\Storage;

class SitePopup extends Component
{
    public bool $isActive = false;
    public $imageUrl = null;
    public $linkUrl = null;

    public function mount()
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
            $this->isActive = false; // Disable if no image is present
        }
    }

    public function render()
    {
        // Normalize before rendering to ensure views always get strings
        $normalizedLink = is_array($this->linkUrl) ? reset($this->linkUrl) : $this->linkUrl;
        $normalizedImage = is_array($this->imageUrl) ? reset($this->imageUrl) : $this->imageUrl;

        return view('livewire.frontend.site-popup', [
            'link' => is_string($normalizedLink) ? $normalizedLink : null,
            'image' => is_string($normalizedImage) ? $normalizedImage : null,
        ]);
    }
}
