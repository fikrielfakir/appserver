<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('devices', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('app_id');
            $table->text('fcm_token');
            $table->string('country')->nullable();
            $table->string('app_version')->nullable();
            $table->integer('android_version')->nullable();
            $table->string('device_manufacturer')->nullable();
            $table->string('device_model')->nullable();
            $table->timestamp('last_seen')->useCurrent();
            $table->timestamp('created_at')->useCurrent();
            
            $table->foreign('app_id')->references('id')->on('apps')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('devices');
    }
};
