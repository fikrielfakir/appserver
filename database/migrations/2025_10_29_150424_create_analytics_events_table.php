<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('analytics_events', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('app_id');
            $table->uuid('account_id')->nullable();
            $table->string('event_type');
            $table->string('ad_type')->nullable();
            $table->integer('value')->nullable();
            $table->string('country')->nullable();
            $table->timestamp('timestamp')->useCurrent();
            
            $table->foreign('app_id')->references('id')->on('apps')->onDelete('cascade');
            $table->foreign('account_id')->references('id')->on('admob_accounts')->onDelete('set null');
            $table->index('app_id');
            $table->index('event_type');
            $table->index('timestamp');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('analytics_events');
    }
};
