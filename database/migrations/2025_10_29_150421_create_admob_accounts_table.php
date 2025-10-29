<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('admob_accounts', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('app_id');
            $table->string('account_name');
            $table->string('status')->default('active');
            $table->integer('priority')->default(1);
            $table->integer('weight')->default(50);
            $table->string('banner_id')->nullable();
            $table->string('interstitial_id')->nullable();
            $table->string('rewarded_id')->nullable();
            $table->string('app_open_id')->nullable();
            $table->string('native_id')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
            
            $table->foreign('app_id')->references('id')->on('apps')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('admob_accounts');
    }
};
