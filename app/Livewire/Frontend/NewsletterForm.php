<?php

namespace App\Livewire\Frontend;

use Livewire\Component;

class NewsletterForm extends Component
{
    public string $email = '';

    public function subscribe()
    {
        $this->validate([
            'email' => 'required|email|max:255',
        ]);

        $userData = \App\Services\GoogleTagService::formatUserData(null, $this->email);
        $this->dispatch('gtag-event', name: 'generate_lead', params: [
            'currency' => 'TRY',
            'value' => 0,
            'lead_source' => 'newsletter',
        ], user_data: $userData);

        $this->reset('email');
        session()->flash('message', 'Bültenimize başarıyla abone oldunuz.');
    }

    public function render()
    {
        return view('livewire.frontend.newsletter-form');
    }
}
