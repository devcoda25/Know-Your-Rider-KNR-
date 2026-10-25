<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->string('name')->nullable();
            $table->string('phone')->unique(); // +256XXXXXXXXX
            $table->string('pin_hash')->nullable();
            // active | pending_approval | suspended
            $table->string('status')->default('active');
            $table->foreignUlid('organization_id')->nullable()->constrained('organizations')->nullOnDelete();
            $table->timestamp('phone_verified_at')->nullable();
            $table->timestamp('last_login_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
