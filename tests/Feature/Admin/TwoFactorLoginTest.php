<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use Filament\Auth\MultiFactor\Email\Notifications\VerifyEmailAuthentication;
use Filament\Auth\Pages\Login;
use Filament\Facades\Filament;
use Illuminate\Support\Facades\Notification;
use Livewire\Livewire;

class TwoFactorLoginTest extends AdminTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Filament::setCurrentPanel('admin');
    }

    public function test_password_alone_does_not_sign_in_and_an_email_code_is_sent(): void
    {
        Notification::fake();
        $admin = User::factory()->create(['is_admin' => true, 'has_email_authentication' => true]);

        Livewire::test(Login::class)
            ->fillForm(['email' => $admin->email, 'password' => 'password'])
            ->call('authenticate')
            ->assertNoRedirect();

        $this->assertGuest();
        Notification::assertSentTo($admin, VerifyEmailAuthentication::class);
    }

    public function test_admin_signs_in_with_password_and_emailed_code(): void
    {
        Notification::fake();
        $admin = User::factory()->create(['is_admin' => true, 'has_email_authentication' => true]);

        $login = Livewire::test(Login::class)
            ->fillForm(['email' => $admin->email, 'password' => 'password'])
            ->call('authenticate');

        $code = null;
        Notification::assertSentTo($admin, VerifyEmailAuthentication::class, function (VerifyEmailAuthentication $notification) use (&$code): bool {
            $code = $notification->code;

            return true;
        });

        $login
            ->set('data.multiFactor.email_code.code', $code)
            ->call('authenticate')
            ->assertRedirect(Filament::getUrl());

        $this->assertAuthenticatedAs($admin);
    }

    public function test_wrong_code_is_rejected(): void
    {
        Notification::fake();
        $admin = User::factory()->create(['is_admin' => true, 'has_email_authentication' => true]);

        $login = Livewire::test(Login::class)
            ->fillForm(['email' => $admin->email, 'password' => 'password'])
            ->call('authenticate');

        $code = null;
        Notification::assertSentTo($admin, VerifyEmailAuthentication::class, function (VerifyEmailAuthentication $notification) use (&$code): bool {
            $code = $notification->code;

            return true;
        });

        $login
            ->set('data.multiFactor.email_code.code', str_pad((string) (((int) $code + 1) % 1_000_000), 6, '0', STR_PAD_LEFT))
            ->call('authenticate')
            ->assertHasErrors();

        $this->assertGuest();
    }

    public function test_admin_without_two_factor_is_sent_to_set_it_up(): void
    {
        $admin = User::factory()->create(['is_admin' => true, 'has_email_authentication' => false]);

        $this->actingAs($admin)
            ->get('/management')
            ->assertRedirect(Filament::getSetUpRequiredMultiFactorAuthenticationUrl());

        $this->actingAs($admin)
            ->get(Filament::getSetUpRequiredMultiFactorAuthenticationUrl())
            ->assertOk();
    }
}
