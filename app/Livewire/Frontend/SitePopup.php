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
        $settings = Setting::whereIn('key', ['popup_active', 'popup_image', 'popup_link'])
            ->pluck('value', 'key')
            ->toArray();

        $this->isActive = isset($settings['popup_active']) && $settings['popup_active'] == '1';

        if ($this->isActive && !empty($settings['popup_image'])) {
            $this->imageUrl = Storage::disk('public')->url($settings['popup_image']);
            $this->linkUrl = $settings['popup_link'] ?? null;
        } else {
            $this->isActive = false; // Disable if no image is present
        }
    }

    public function render()
    {
        return view('livewire.frontend.site-popup');
    }
}
