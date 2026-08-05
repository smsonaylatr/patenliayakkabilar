<?php

namespace App\Livewire\Frontend;

use App\Models\GiveawayEntry as EntryModel;
use Livewire\Component;
use Illuminate\Support\Str;

class GiveawayEntry extends Component
{
    public $name = '';
    public $instagram_username = '';
    public $phone = '';
    public $email = '';
    public $shoe_size = '';
    public $city = '';
    public $district = '';
    public $address = '';
    
    public $followed_instagram = false;
    public $kvkk_consent = false;
    public $sms_consent = true;

    public $cities = [];
    public $districts = [];

    public $isSuccess = false;
    public $ticketCode = null;
    public $alreadyParticipated = false;
    public $existingTicketCode = null;

    protected $rules = [
        'name' => 'required|string|min:3|max:100',
        'instagram_username' => 'required|string|min:2|max:100',
        'phone' => 'required|string|min:10|max:20',
        'email' => 'required|email|max:150',
        'shoe_size' => 'required|string',
        'city' => 'required|string',
        'district' => 'required|string',
        'address' => 'nullable|string|max:500',
        'followed_instagram' => 'accepted',
        'kvkk_consent' => 'accepted',
    ];

    protected $messages = [
        'name.required' => 'Lütfen adınızı ve soyadınızı giriniz.',
        'name.min' => 'Ad Soyad en az 3 karakter olmalıdır.',
        'instagram_username.required' => 'Instagram kullanıcı adınızı giriniz.',
        'phone.required' => 'Telefon numaranızı giriniz.',
        'phone.min' => 'Geçerli bir telefon numarası giriniz (Örn: 05551234567).',
        'email.required' => 'E-posta adresinizi giriniz.',
        'email.email' => 'Geçerli bir e-posta adresi giriniz.',
        'shoe_size.required' => 'Lütfen tercih ettiğiniz ayakkabı numarasını seçiniz.',
        'city.required' => 'Lütfen yaşadığınız ili seçiniz.',
        'district.required' => 'Lütfen ilçenizi seçiniz.',
        'followed_instagram.accepted' => 'Çekilişe katılmak için Instagram sayfamızı takip ettiğinizi onaylamalısınız.',
        'kvkk_consent.accepted' => 'Çekiliş katılım koşullarını ve KVKK metnini onaylamalısınız.',
    ];

    public function mount()
    {
        if (file_exists(database_path('data/cities.json'))) {
            $json = json_decode(file_get_contents(database_path('data/cities.json')), true);
            if (isset($json['data'])) {
                $this->cities = collect($json['data'])->pluck('name')->toArray();
            }
        }
    }

    public function updatedCity($value)
    {
        $this->district = '';
        $this->districts = [];

        if ($value && file_exists(database_path('data/cities.json'))) {
            $json = json_decode(file_get_contents(database_path('data/cities.json')), true);
            if (isset($json['data'])) {
                $cityData = collect($json['data'])->firstWhere('name', $value);
                if ($cityData && isset($cityData['districts'])) {
                    $this->districts = collect($cityData['districts'])->pluck('name')->toArray();
                }
            }
        }
    }

    public function submit()
    {
        $this->validate();

        $cleanInstagram = '@' . ltrim(trim($this->instagram_username), '@');
        $cleanPhone = preg_replace('/[^0-9]/', '', $this->phone);

        // Zaten katılım var mı kontrol et
        $existing = EntryModel::where(function ($q) use ($cleanInstagram, $cleanPhone) {
            $q->where('instagram_username', 'LIKE', $cleanInstagram)
              ->orWhere('phone', 'LIKE', '%' . substr($cleanPhone, -10));
        })->first();

        if ($existing) {
            $this->alreadyParticipated = true;
            $this->existingTicketCode = $existing->ticket_code;
            return;
        }

        $ticketCode = EntryModel::generateTicketCode();

        EntryModel::create([
            'ticket_code' => $ticketCode,
            'name' => trim($this->name),
            'instagram_username' => $cleanInstagram,
            'phone' => trim($this->phone),
            'email' => strtolower(trim($this->email)),
            'shoe_size' => $this->shoe_size,
            'city' => $this->city,
            'district' => $this->district,
            'address' => trim($this->address),
            'kvkk_consent' => $this->kvkk_consent,
            'sms_consent' => $this->sms_consent,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);

        $this->ticketCode = $ticketCode;
        $this->isSuccess = true;
    }

    public function render()
    {
        return view('livewire.frontend.giveaway-entry')
            ->layout('components.layouts.giveaway', [
                'title' => 'Instagram Büyük Çekiliş Katılım Formu | Patenli Ayakkabılar',
                'description' => 'Patenli Ayakkabılar resmi Instagram çekilişine katılın! 1 Çifte istediği ışıklı patenli ayakkabı HEDİYE! Hemen form doldurun, kura numaranızı alın.'
            ]);
    }
}
