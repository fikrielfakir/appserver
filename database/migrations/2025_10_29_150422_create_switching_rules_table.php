<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('switching_rules', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('app_id')->unique();
            $table->string('strategy')->default('weighted_random');
            $table->string('rotation_interval')->default('daily');
            $table->boolean('fallback_enabled')->default(true);
            $table->boolean('ab_testing_enabled')->default(false);
            $table->json('geographic_rules')->nullable();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
            
            $table->foreign('app_id')->references('id')->on('apps')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('switching_rules');
    }
};
