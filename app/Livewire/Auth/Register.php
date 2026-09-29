<?php

namespace App\Livewire\Auth;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;

class Register extends Component
{
    public string $name = '';
    public string $email = '';
    public string $password = '';
    public string $password_confirmation = '';

    protected array $rules = [
        'name' => 'required|string|max:255',
        'email' => 'required|string|email|max:255|unique:users,email',
        'password' => 'required|string|min:8|confirmed',
    ];

    protected array $messages = [
        'name.required' => 'Lütfen adınızı ve soyadınızı giriniz.',
        'email.required' => 'Lütfen e-posta adresinizi giriniz.',
        'email.email' => 'Lütfen geçerli bir e-posta adresi giriniz.',
        'email.unique' => 'Bu e-posta adresi ile zaten kayıtlı bir hesap bulunuyor.',
        'password.required' => 'Lütfen bir şifre belirleyiniz.',
        'password.min' => 'Şifreniz en az 8 karakter olmalıdır.',
        'password.confirmed' => 'Girdiğiniz şifreler birbiriyle eşleşmiyor.',
    ];

    public function register()
    {
        $this->validate();

        $user = User::create([
            'name' => trim($this->name),
            'email' => strtolower(trim($this->email)),
            'password' => Hash::make($this->password),
        ]);

        Auth::login($user);
        session()->regenerate();

        return redirect()->intended('/hesabim');
    }

    public function render()
    {
        return view('livewire.auth.register')
            ->layout('components.layouts.app', ['title' => 'Kayıt Ol | Patenli Ayakkabılar', 'robots' => 'noindex, nofollow']);
    }
}
