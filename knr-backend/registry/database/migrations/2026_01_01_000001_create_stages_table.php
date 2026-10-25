<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stages', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->string('name');
            $table->string('district');
            $table->string('division');
            $table->string('parish')->nullable();
            $table->string('village')->nullable();
            $table->string('status')->default('pending'); // pending | active | suspended
            $table->string('head_user_id')->nullable()->index(); // user id in the auth service
            $table->string('contact_name')->nullable();
            $table->string('contact_phone')->nullable();
            $table->string('subscription_status')->default('trial'); // trial | active | past_due | cancelled
            $table->timestamps();

            $table->index(['district', 'division']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stages');
    }
};
