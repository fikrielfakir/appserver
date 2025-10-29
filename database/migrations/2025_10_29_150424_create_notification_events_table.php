<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notification_events', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('notification_id');
            $table->uuid('device_id')->nullable();
            $table->string('event_type');
            $table->timestamp('timestamp')->useCurrent();
            
            $table->foreign('notification_id')->references('id')->on('notifications')->onDelete('cascade');
            $table->foreign('device_id')->references('id')->on('devices')->onDelete('set null');
            $table->index('notification_id');
            $table->index('event_type');
            $table->index('timestamp');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notification_events');
    }
};
