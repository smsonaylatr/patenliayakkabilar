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

        $this->reset('email');
        session()->flash('message', 'Bltenimize baaryla abone oldunuz.');
    }

    public function render()
    {
        return view('livewire.frontend.newsletter-form');
    }
}
