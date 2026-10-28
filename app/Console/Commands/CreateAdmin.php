<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

/**
 * Creates a dashboard admin (or resets one's password) without a terminal: it
 * is meant to be run once from a cPanel cron job. The generated password is
 * written to a private file, never to the output, and while that file exists
 * the command changes nothing, so a cron job left running is harmless.
 */
#[Signature('app:create-admin
    {email : The email address to sign in with}
    {--name=Admin : The name shown in the dashboard}
    {--reset : Set a new password if the account already exists}')]
#[Description('Create a dashboard admin, or reset its password, and save the password to a private file')]
class CreateAdmin extends Command
{
    public const CREDENTIALS_FILE = 'admin-credentials.txt';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $email = mb_strtolower(trim($this->argument('email')));

        if (Validator::make(['email' => $email], ['email' => ['required', 'email']])->fails()) {
            $this->components->error("[{$email}] is not a valid email address.");

            return self::FAILURE;
        }

        $disk = Storage::disk('local');

        if ($disk->exists(self::CREDENTIALS_FILE)) {
            $this->components->warn('Nothing changed: '.self::CREDENTIALS_FILE.' already exists. Delete it to run this command again.');

            return self::SUCCESS;
        }

        $user = User::query()->where('email', $email)->first();

        if ($user && ! $this->option('reset')) {
            $this->components->warn("Nothing changed: [{$email}] already exists. Add --reset to give it a new password.");

            return self::SUCCESS;
        }

        $password = Str::password(18, symbols: false);

        if ($user) {
            $user->forceFill(['password' => $password, 'is_admin' => true])->save();
        } else {
            User::query()->create([
                'name' => $this->option('name'),
                'email' => $email,
                'password' => $password,
                'is_admin' => true,
            ]);
        }

        $disk->put(self::CREDENTIALS_FILE, implode(PHP_EOL, [
            'Dashboard / لوحة التحكم: '.url('/management'),
            "Email / البريد: {$email}",
            "Password / كلمة المرور: {$password}",
            'Created / تاريخ الإنشاء: '.now()->toDateTimeString(),
            '',
            'Sign in, change the password from your profile, then delete this file.',
            'سجّل الدخول، وغيّر كلمة المرور من ملفك الشخصي، ثم احذف هذا الملف.',
        ]).PHP_EOL);

        chmod($disk->path(self::CREDENTIALS_FILE), 0600);

        $this->components->info(($user ? 'Password reset' : 'Admin created').' for ['.$email.']. The password is in storage/app/private/'.self::CREDENTIALS_FILE.'.');

        return self::SUCCESS;
    }
}
