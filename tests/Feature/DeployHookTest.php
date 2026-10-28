<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

class DeployHookTest extends TestCase
{
    private const TOKEN = 'deploy-token-for-tests';

    protected function setUp(): void
    {
        parent::setUp();

        config(['site.deploy_token' => self::TOKEN]);
    }

    public function test_the_hook_is_disabled_without_a_configured_token(): void
    {
        config(['site.deploy_token' => null]);
        Artisan::spy();

        $this->withToken('')->post('/deploy/hook')->assertNotFound();

        Artisan::shouldNotHaveReceived('call');
    }

    public function test_a_wrong_token_runs_nothing(): void
    {
        Artisan::spy();

        $this->withToken('guess')->post('/deploy/hook')->assertNotFound();

        Artisan::shouldNotHaveReceived('call');
    }

    public function test_updates_the_database_clears_caches_and_links_storage(): void
    {
        Artisan::shouldReceive('call')->with('migrate', ['--force' => true])->once()->andReturn(0);
        Artisan::shouldReceive('call')->with('optimize:clear', [])->once()->andReturn(0);
        Artisan::shouldReceive('call')->with('storage:link', ['--force' => true])->once()->andReturn(0);
        Artisan::shouldReceive('output')->andReturn('done');

        $this->withToken(self::TOKEN)->post('/deploy/hook')
            ->assertOk()
            ->assertJson(['ok' => true]);
    }

    public function test_a_failed_migration_fails_the_deploy(): void
    {
        Artisan::shouldReceive('call')->with('migrate', ['--force' => true])->andReturn(1);
        Artisan::shouldReceive('call')->andReturn(0);
        Artisan::shouldReceive('output')->andReturn('SQLSTATE error');

        $this->withToken(self::TOKEN)->post('/deploy/hook')
            ->assertServerError()
            ->assertJson(['ok' => false]);
    }

    public function test_a_host_that_forbids_symlinks_does_not_fail_the_deploy(): void
    {
        Artisan::shouldReceive('call')->with('storage:link', ['--force' => true])->andThrow(new \RuntimeException('symlink() has been disabled'));
        Artisan::shouldReceive('call')->andReturn(0);
        Artisan::shouldReceive('output')->andReturn('done');

        $this->withToken(self::TOKEN)->post('/deploy/hook')->assertOk()->assertJson(['ok' => true]);
    }
}
