<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bikes', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('rider_id')->constrained()->cascadeOnDelete();
            $table->string('plate');                       // as entered, e.g. UBK 482X
            $table->string('plate_normalized')->unique();  // UBK482X, used for search
            $table->string('make');
            $table->string('model')->nullable();
            $table->string('color')->nullable();
            $table->string('photo_path')->nullable();
            $table->timestamp('stolen_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bikes');
    }
};
