<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('documents', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('rider_id')->constrained()->cascadeOnDelete();
            $table->string('type'); // permit | insurance
            $table->string('number')->nullable();
            $table->string('issuer')->nullable(); // insurer name or licensing office
            $table->date('expires_at');
            $table->string('file_path')->nullable();
            $table->timestamps();

            $table->index(['rider_id', 'type']);
            $table->index('expires_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('documents');
    }
};
