<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Modules modelled on the big consulting firms' sites: thought leadership
 * (insights), leadership profiles and client case studies.
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('team_members', function (Blueprint $table) {
            $table->id();
            $table->json('name');
            $table->json('position');
            $table->json('bio')->nullable();
            $table->string('photo')->nullable();
            $table->string('linkedin_url')->nullable();
            $table->boolean('is_leadership')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('insights', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->string('type')->index();
            $table->json('title');
            $table->json('excerpt')->nullable();
            $table->json('body')->nullable();
            $table->string('cover')->nullable();
            $table->string('attachment')->nullable();
            $table->foreignId('service_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('author_id')->nullable()->constrained('team_members')->nullOnDelete();
            $table->boolean('is_featured')->default(false);
            $table->timestamp('published_at')->nullable()->index();
            $table->timestamps();
        });

        Schema::create('case_studies', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->json('title');
            $table->json('client')->nullable();
            $table->json('sector')->nullable();
            $table->foreignId('service_id')->nullable()->constrained()->nullOnDelete();
            $table->json('summary')->nullable();
            $table->json('challenge')->nullable();
            $table->json('approach')->nullable();
            $table->json('outcome')->nullable();
            $table->json('metrics')->nullable();
            $table->string('cover')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('case_studies');
        Schema::dropIfExists('insights');
        Schema::dropIfExists('team_members');
    }
};
