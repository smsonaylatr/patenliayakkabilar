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

    public function login()
    {
        $this->validate();

        if (Auth::attempt(['email' => $this->email, 'password' => $this->password], $this->remember)) {
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
