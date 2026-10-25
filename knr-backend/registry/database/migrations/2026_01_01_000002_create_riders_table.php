<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('riders', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('stage_id')->constrained()->cascadeOnDelete();
            $table->string('full_name');
            $table->string('phone')->nullable();
            $table->text('national_id'); // encrypted at rest (model cast)
            $table->string('national_id_hash')->unique(); // HMAC for duplicate checks
            $table->date('date_of_birth')->nullable();
            $table->string('address')->nullable();
            $table->string('next_of_kin_name')->nullable();
            $table->string('next_of_kin_phone')->nullable();
            $table->string('photo_path')->nullable();
            $table->string('qr_token', 64)->unique(); // random, unguessable; QR encodes a URL with this
            $table->string('status')->default('active'); // active | suspended | removed
            $table->string('suspension_reason')->nullable();
            $table->timestamp('consent_at');
            $table->string('registered_by')->nullable(); // user id in the auth service
            $table->timestamps();

            $table->index(['stage_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('riders');
    }
};
