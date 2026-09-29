<?php

namespace App\Livewire\Auth;

use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Login extends Component
{
    public string $email = '';
    public string $password = '';
    public bool $remember = false;

    protected array $rules = [
        'email' => 'required|email',
        'password' => 'required',
    ];

    protected array $messages = [
        'email.required' => 'Lütfen e-posta adresinizi giriniz.',
        'email.email' => 'Lütfen geçerli bir e-posta adresi giriniz.',
        'password.required' => 'Lütfen şifrenizi giriniz.',
    ];

    public function login()
    {
        $this->validate();

        if (Auth::attempt(['email' => strtolower(trim($this->email)), 'password' => $this->password], $this->remember)) {
            session()->regenerate();
            return redirect()->intended('/hesabim');
        }

        $this->addError('email', 'Verilen bilgiler kayıtlarımızla eşleşmiyor.');
    }

    public function render()
    {
        return view('livewire.auth.login')
            ->layout('components.layouts.app', ['title' => 'Giriş Yap | Patenli Ayakkabılar', 'robots' => 'noindex, nofollow']);
    }
}
