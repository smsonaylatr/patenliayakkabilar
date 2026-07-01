<?php

namespace App\Livewire\Frontend;

use App\Models\ContactMessage;
use Livewire\Component;

class Contact extends Component
{
    public $name = '';
    public $email = '';
    public $subject = '';
    public $message = '';

    public $isSuccess = false;

    protected $rules = [
        'name' => 'required|min:3',
        'email' => 'required|email',
        'subject' => 'nullable|min:3',
        'message' => 'required|min:10',
    ];

    public function submit()
    {
        $this->validate();

        ContactMessage::create([
            'name' => $this->name,
            'email' => $this->email,
            'subject' => $this->subject,
            'message' => $this->message,
        ]);

        $this->isSuccess = true;
        
        $this->reset(['name', 'email', 'subject', 'message']);
        
        $this->dispatch('toast-notify', type: 'success', message: 'Mesajınız başarıyla alındı. En kısa sürede dönüş yapacağız!');
    }

    public function render()
    {
        return view('livewire.frontend.contact')
            ->layout('components.layouts.app', ['title' => 'İletişim | Patenli Ayakkabılar']);
    }
}
