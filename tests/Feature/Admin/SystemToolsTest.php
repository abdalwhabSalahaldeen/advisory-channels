<?php

namespace Tests\Feature\Admin;

use App\Filament\Pages\ManageSystem;
use App\Models\User;
use Illuminate\Support\Facades\Artisan;
use Livewire\Livewire;

class SystemToolsTest extends AdminTestCase
{
    public function test_shows_the_server_details(): void
    {
        $this->actingAs($this->admin)
            ->get(ManageSystem::getUrl())
            ->assertOk()
            ->assertSee(PHP_VERSION)
            ->assertSee(app()->version());
    }

    public function test_updates_the_database_without_a_terminal(): void
    {
        Artisan::spy();
        $this->actingAs($this->admin);

        Livewire::test(ManageSystem::class)
            ->callAction('migrate')
            ->assertNotified();

        Artisan::shouldHaveReceived('call')->with('migrate', ['--force' => true]);
    }

    public function test_clears_the_caches(): void
    {
        Artisan::spy();
        $this->actingAs($this->admin);

        Livewire::test(ManageSystem::class)->callAction('clearCache');

        Artisan::shouldHaveReceived('call')->with('optimize:clear', []);
    }

    public function test_reports_a_failing_command(): void
    {
        Artisan::shouldReceive('call')->andThrow(new \RuntimeException('Disk full'));
        $this->actingAs($this->admin);

        Livewire::test(ManageSystem::class)
            ->callAction('storageLink')
            ->assertNotified(__('admin.system.actions.storageLink'));
    }

    public function test_non_admins_cannot_open_it(): void
    {
        $this->actingAs(User::factory()->create(['is_admin' => false]))
            ->get(ManageSystem::getUrl())
            ->assertForbidden();
    }
}
