<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notifications', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('app_id');
            $table->string('title');
            $table->text('message');
            $table->string('type')->default('popup');
            $table->string('priority')->default('normal');
            $table->string('status')->default('draft');
            
            $table->json('target_countries')->nullable();
            $table->json('target_app_versions')->nullable();
            $table->integer('min_android_version')->nullable();
            $table->json('user_segments')->nullable();
            
            $table->timestamp('start_date')->nullable();
            $table->timestamp('end_date')->nullable();
            $table->boolean('recurring')->default(false);
            $table->string('frequency')->default('once');
            
            $table->string('image_url')->nullable();
            $table->string('action_button_text')->nullable();
            $table->string('action_type')->nullable();
            $table->text('action_value')->nullable();
            $table->boolean('cancelable')->default(true);
            
            $table->integer('max_displays')->default(1);
            $table->integer('display_interval_hours')->default(24);
            $table->boolean('show_on_app_launch')->default(false);
            
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
            
            $table->foreign('app_id')->references('id')->on('apps')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
