<?php

namespace Tests\Feature;

use App\Livewire\Frontend\SitePopup;
use App\Models\Setting;
use App\Services\ExceptionTelegramReporter;
use App\Services\TelegramAlertService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Livewire\Features\SupportLockedProperties\CannotUpdateLockedPropertyException;
use Livewire\Livewire;
use Tests\TestCase;

class SitePopupTest extends TestCase
{
    use RefreshDatabase;

    public function test_site_popup_renders_cleanly_when_active(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('popups/test.jpg', 'fake-image');

        Setting::create(['key' => 'popup_active', 'value' => '1']);
        Setting::create(['key' => 'popup_image', 'value' => 'popups/test.jpg']);
        Setting::create(['key' => 'popup_link', 'value' => 'https://patenliayakkabilar.com/kampanya']);

        Livewire::test(SitePopup::class)
            ->assertSet('isActive', true)
            ->assertSee('https://patenliayakkabilar.com/kampanya');
    }

    public function test_updating_link_url_does_not_throw_cannot_update_locked_property_exception(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('popups/test.jpg', 'fake-image');

        Setting::create(['key' => 'popup_active', 'value' => '1']);
        Setting::create(['key' => 'popup_image', 'value' => 'popups/test.jpg']);
        Setting::create(['key' => 'popup_link', 'value' => 'https://patenliayakkabilar.com/orijinal']);

        // Daha önce locked olduğu için set('linkUrl', ...) çağrıldığında CannotUpdateLockedPropertyException fırlatıyordu
        Livewire::test(SitePopup::class)
            ->set('linkUrl', 'https://attacker.com/malicious')
            ->assertStatus(200)
            ->assertSee('https://patenliayakkabilar.com/orijinal')
            ->assertDontSee('https://attacker.com/malicious');
    }

    public function test_exception_telegram_reporter_ignores_cannot_update_locked_property_exception(): void
    {
        $mockTelegram = $this->createMock(TelegramAlertService::class);
        $mockTelegram->expects($this->never())->method('sendAlert');

        $reporter = new ExceptionTelegramReporter($mockTelegram);
        $exception = new CannotUpdateLockedPropertyException('linkUrl');

        $reporter->report($exception);
    }
}
