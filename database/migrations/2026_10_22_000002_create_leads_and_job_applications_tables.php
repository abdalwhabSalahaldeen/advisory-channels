<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('leads', function (Blueprint $table) {
            $table->id();
            $table->string('type')->index();
            $table->string('status')->default('new')->index();
            $table->string('full_name');
            $table->string('company')->nullable();
            $table->string('job_title')->nullable();
            $table->string('email');
            $table->string('phone')->nullable();
            $table->string('country')->nullable();
            $table->foreignId('service_id')->nullable()->constrained()->nullOnDelete();
            $table->json('packages')->nullable();
            $table->string('timeline')->nullable();
            $table->text('message')->nullable();
            $table->text('notes')->nullable();
            $table->string('locale', 5);
            $table->string('source_url')->nullable();
            $table->timestamp('consented_at');
            $table->timestamps();
        });

        Schema::create('job_applications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('job_opening_id')->nullable()->constrained()->nullOnDelete();
            $table->string('status')->default('new')->index();
            $table->string('full_name');
            $table->string('email');
            $table->string('phone');
            $table->string('linkedin_url')->nullable();
            $table->string('cv_path');
            $table->text('cover_letter')->nullable();
            $table->text('notes')->nullable();
            $table->string('locale', 5);
            $table->timestamp('consented_at');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('job_applications');
        Schema::dropIfExists('leads');
    }
};
