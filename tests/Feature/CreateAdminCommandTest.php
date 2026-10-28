<?php

namespace Tests\Feature;

use App\Console\Commands\CreateAdmin;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class CreateAdminCommandTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('local');
    }

    public function test_creates_an_admin_and_saves_the_password_to_a_private_file(): void
    {
        $this->artisan('app:create-admin', ['email' => 'Owner@Example.com', '--name' => 'Site Owner'])
            ->doesntExpectOutputToContain('Password')
            ->assertSuccessful();

        $admin = User::query()->sole();
        $this->assertSame('owner@example.com', $admin->email);
        $this->assertSame('Site Owner', $admin->name);
        $this->assertTrue($admin->is_admin);
        $this->assertTrue(Hash::check($this->savedPassword(), $admin->password));
        $this->assertSame('0600', substr(sprintf('%o', fileperms(Storage::disk('local')->path(CreateAdmin::CREDENTIALS_FILE))), -4));
    }

    public function test_a_cron_job_left_running_changes_nothing_while_the_file_exists(): void
    {
        $this->artisan('app:create-admin', ['email' => 'owner@example.com'])->assertSuccessful();
        $password = $this->savedPassword();

        $this->artisan('app:create-admin', ['email' => 'owner@example.com', '--reset' => true])->assertSuccessful();

        $this->assertSame($password, $this->savedPassword());
        $this->assertTrue(Hash::check($password, User::query()->sole()->password));
    }

    public function test_an_existing_account_is_left_alone_without_reset(): void
    {
        $user = User::factory()->create(['email' => 'owner@example.com', 'password' => 'original-password']);

        $this->artisan('app:create-admin', ['email' => 'owner@example.com'])->assertSuccessful();

        $this->assertTrue(Hash::check('original-password', $user->fresh()->password));
        Storage::disk('local')->assertMissing(CreateAdmin::CREDENTIALS_FILE);
    }

    public function test_reset_gives_an_existing_account_a_new_password_and_admin_access(): void
    {
        $user = User::factory()->create(['email' => 'owner@example.com', 'password' => 'original-password', 'is_admin' => false]);

        $this->artisan('app:create-admin', ['email' => 'owner@example.com', '--reset' => true])->assertSuccessful();

        $user->refresh();
        $this->assertTrue($user->is_admin);
        $this->assertTrue(Hash::check($this->savedPassword(), $user->password));
    }

    public function test_rejects_an_invalid_email(): void
    {
        $this->artisan('app:create-admin', ['email' => 'not-an-email'])->assertFailed();

        $this->assertDatabaseCount('users', 0);
    }

    private function savedPassword(): string
    {
        preg_match('/Password \/ كلمة المرور: (\S+)/u', Storage::disk('local')->get(CreateAdmin::CREDENTIALS_FILE), $match);

        return $match[1];
    }
}
