<?php

namespace App\Livewire\Product;

use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\StockNotification;
use Livewire\Component;
use Livewire\Attributes\On;
use Illuminate\Support\Facades\Auth;

class StockNotificationModal extends Component
{
    public bool $isOpen = false;
    public ?Product $product = null;
    public ?int $productId = null;
    public ?int $variantId = null;
    
    public string $email = '';
    public string $phone = '';
    public bool $kvkkConsent = true;

    public bool $isSuccess = false;
    public string $message = '';

    #[On('open-stock-modal')]
    public function openModal(?int $productId = null, ?int $variantId = null)
    {
        if ($productId) {
            $this->productId = $productId;
            $this->product = Product::find($productId);
        }

        $this->variantId = $variantId ?: null;

        if (Auth::check()) {
            $this->email = Auth::user()->email ?? '';
            $this->phone = Auth::user()->phone ?? '';
        }

        $this->isSuccess = false;
        $this->message = '';
        $this->isOpen = true;
    }

    public function closeModal()
    {
        $this->isOpen = false;
    }

    public function submit()
    {
        $this->validate([
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:20',
            'kvkkConsent' => 'accepted',
        ], [
            'email.required' => 'Lütfen geçerli bir e-posta adresi giriniz.',
            'email.email' => 'Lütfen geçerli bir e-posta adresi giriniz.',
            'kvkkConsent.accepted' => 'Devam etmek için aydınlatma metnini onaylamalısınız.',
        ]);

        if (!$this->productId) {
            return;
        }

        // Çift kayıt kontrolü
        $existing = StockNotification::where('product_id', $this->productId)
            ->where('product_variant_id', $this->variantId)
            ->where('email', $this->email)
            ->where('is_notified', false)
            ->first();

        if ($existing) {
            $this->isSuccess = true;
            $this->message = 'Bu ürün için stok bildirimi talebiniz zaten alınmıştır. Stoklar yenilendiğinde sizi bilgilendireceğiz!';
            return;
        }

        StockNotification::create([
            'product_id' => $this->productId,
            'product_variant_id' => $this->variantId,
            'user_id' => Auth::id(),
            'email' => $this->email,
            'phone' => $this->phone,
            'ip_address' => request()->ip(),
        ]);

        $this->isSuccess = true;
        $this->message = 'Talebiniz başarıyla alındı! Ürün stoklarımıza girdiğinde size e-posta/SMS ile haber vereceğiz.';
    }

    public function render()
    {
        $selectedVariant = $this->variantId ? ProductVariant::find($this->variantId) : null;

        return view('livewire.product.stock-notification-modal', [
            'selectedVariant' => $selectedVariant,
        ]);
    }
}
