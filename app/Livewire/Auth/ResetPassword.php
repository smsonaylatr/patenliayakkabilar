<?php

namespace App\Livewire\Auth;

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Livewire\Attributes\Locked;
use Livewire\Component;
use App\Models\User;

class ResetPassword extends Component
{
    #[Locked]
    public ?string $token = null;
    
    public string $email = '';
    public string $password = '';
    public string $password_confirmation = '';

    #[Locked]
    public string $status = '';

    public function mount($token)
    {
        $this->token = (string) $token;
        $this->email = (string) request()->query('email');
    }

    public function resetPassword()
    {
        $this->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:8|confirmed',
        ], [
            'token.required' => 'Şifre sıfırlama kodu geçersiz.',
            'email.required' => 'Lütfen e-posta adresinizi giriniz.',
            'email.email' => 'Lütfen geçerli bir e-posta adresi giriniz.',
            'password.required' => 'Lütfen yeni bir şifre belirleyiniz.',
            'password.min' => 'Şifreniz en az 8 karakter olmalıdır.',
            'password.confirmed' => 'Girdiğiniz şifreler birbiriyle eşleşmiyor.',
        ]);

        $status = Password::broker()->reset(
            [
                'email' => $this->email,
                'password' => $this->password,
                'password_confirmation' => $this->password_confirmation,
                'token' => $this->token,
            ],
            function (User $user, string $password) {
                $user->password = Hash::make($password);
                $user->setRememberToken(Str::random(60));
                $user->save();
            }
        );

        if ($status === Password::PASSWORD_RESET) {
            session()->flash('success', 'Şifreniz başarıyla sıfırlandı. Yeni şifrenizle giriş yapabilirsiniz.');
            return redirect()->route('login');
        }

        $this->addError('email', 'Token geçersiz veya süresi dolmuş. Lütfen tekrar sıfırlama bağlantısı isteyin.');
    }

    public function render()
    {
        return view('livewire.auth.reset-password')
            ->layout('components.layouts.app', ['title' => 'Yeni Şifre Belirle | Patenli Ayakkabılar', 'robots' => 'noindex, nofollow']);
    }
}
