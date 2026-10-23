<?php

namespace Tests\Feature;

use App\Models\User;
use Filament\Auth\MultiFactor\Email\EmailAuthentication;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class EmailSignInCodeTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function an_emailed_sign_in_code_can_be_verified(): void
    {
        // Guards the cache config: the code's expiry is a Carbon date that must survive the cache round trip.
        config(['cache.default' => 'database']);
        Notification::fake();

        $user = User::factory()->create(['is_admin' => true]);
        $provider = EmailAuthentication::make()->generateCodesUsing(fn (): string => '123456');

        $this->assertTrue($provider->sendCode($user));
        $this->assertFalse($provider->verifyCode('000000', $user));
        $this->assertTrue($provider->verifyCode('123456', $user));
    }
}
