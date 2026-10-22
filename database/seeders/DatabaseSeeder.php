<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            SettingSeeder::class,
            ServiceSeeder::class,
            GatePackageSeeder::class,
            JobOpeningSeeder::class,
        ]);

        // Set ADMIN_EMAIL / ADMIN_PASSWORD in .env before seeding production.
        User::query()->firstOrCreate(
            ['email' => env('ADMIN_EMAIL', 'admin@advisory-channels.com')],
            [
                'name' => 'Admin',
                'password' => env('ADMIN_PASSWORD') ?: Str::password(20),
                'is_admin' => true,
            ],
        );
    }
}
