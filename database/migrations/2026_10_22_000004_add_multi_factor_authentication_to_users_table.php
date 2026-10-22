<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Columns used by Filament's email-code and authenticator-app two-factor providers.
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('has_email_authentication')->default(false)->after('is_admin');
            $table->text('app_authentication_secret')->nullable()->after('has_email_authentication');
            $table->text('app_authentication_recovery_codes')->nullable()->after('app_authentication_secret');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['has_email_authentication', 'app_authentication_secret', 'app_authentication_recovery_codes']);
        });
    }
};
