<?php

namespace App\Livewire\Auth;

use Illuminate\Support\Facades\Password;
use Livewire\Component;

class ForgotPassword extends Component
{
    public $email = '';
    public $status = '';

    protected $rules = [
        'email' => 'required|email',
    ];

    public function sendResetLink()
    {
        $this->validate();

        $status = Password::broker()->sendResetLink(
            ['email' => $this->email]
        );

        if ($status === Password::RESET_LINK_SENT) {
            $this->status = 'Şifre sıfırlama bağlantısı e-posta adresinize gönderildi.';
            $this->email = '';
        } else {
            $this->addError('email', 'Kayıtlı e-posta adresi bulunamadı veya bir hata oluştu.');
        }
    }

    public function render()
    {
        return view('livewire.auth.forgot-password')
            ->layout('components.layouts.app', ['title' => 'Şifremi Unuttum | Patenli Ayakkabılar']);
    }
}
