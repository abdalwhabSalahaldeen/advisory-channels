<?php

namespace Tests\Feature;

use App\Models\Service;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class InstallerTest extends TestCase
{
    use RefreshDatabase;

    private const TOKEN = 'install-token-for-tests';

    protected function setUp(): void
    {
        parent::setUp();

        config([
            'site.install_token' => self::TOKEN,
            // Keep the storage link step away from the project's real public/storage link.
            'filesystems.links' => [storage_path('framework/testing/install-link') => storage_path('app/public')],
        ]);
    }

    protected function tearDown(): void
    {
        File::delete(storage_path('framework/testing/install-link'));

        parent::tearDown();
    }

    public function test_the_installer_is_hidden_without_a_configured_token(): void
    {
        config(['site.install_token' => null]);

        $this->get('/install?token=')->assertNotFound();
        $this->post('/install', $this->validInput())->assertNotFound();
    }

    public function test_a_wrong_token_is_rejected(): void
    {
        $this->get('/install?token=guess')->assertNotFound();
        $this->post('/install', [...$this->validInput(), 'token' => 'guess'])->assertNotFound();

        $this->assertDatabaseCount('users', 0);
    }

    public function test_shows_the_hosting_check_and_the_account_form(): void
    {
        $this->get('/install?token='.self::TOKEN)
            ->assertOk()
            ->assertSee(__('install.checks.database'))
            ->assertSee(__('install.account'));
    }

    public function test_installs_the_content_and_creates_the_first_admin(): void
    {
        $this->post('/install', $this->validInput())
            ->assertOk()
            ->assertSee(__('install.done'));

        $admin = User::query()->sole();
        $this->assertSame('owner@example.com', $admin->email);
        $this->assertTrue($admin->is_admin);
        $this->assertTrue(Hash::check('Strong-pass-2026', $admin->password));
        $this->assertTrue(Service::query()->exists());
    }

    public function test_the_installer_locks_itself_once_an_admin_exists(): void
    {
        $this->post('/install', $this->validInput())->assertOk();

        $this->get('/install?token='.self::TOKEN)->assertNotFound();
        $this->post('/install', [...$this->validInput(), 'email' => 'second@example.com'])->assertNotFound();

        $this->assertDatabaseCount('users', 1);
    }

    public function test_the_admin_password_must_be_strong_and_confirmed(): void
    {
        $this->from('/install?token='.self::TOKEN)
            ->post('/install', [...$this->validInput(), 'password' => 'short', 'password_confirmation' => 'other'])
            ->assertSessionHasErrors('password');

        $this->assertDatabaseCount('users', 0);
    }

    /**
     * @return array<string, string>
     */
    private function validInput(): array
    {
        return [
            'token' => self::TOKEN,
            'name' => 'Site Owner',
            'email' => 'owner@example.com',
            'password' => 'Strong-pass-2026',
            'password_confirmation' => 'Strong-pass-2026',
        ];
    }
}
