<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('incidents', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('rider_id')->constrained()->cascadeOnDelete();
            $table->string('reporter_type'); // passenger | police | lc | other
            $table->string('reporter_user_id')->nullable();
            $table->string('type');          // reckless_riding | overcharging | harassment | offence | other
            $table->text('description')->nullable();
            $table->string('status')->default('open'); // open | reviewed | closed
            $table->timestamps();
        });

        // Every QR scan and plate search is recorded (accountability for authority lookups).
        Schema::create('scan_logs', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->string('actor_user_id')->nullable()->index();
            $table->json('actor_roles')->nullable();
            $table->foreignUlid('rider_id')->nullable()->constrained()->nullOnDelete();
            $table->string('method');  // qr | plate
            $table->string('query');   // token or normalized plate searched
            $table->boolean('found');
            $table->string('ip')->nullable();
            $table->decimal('lat', 10, 7)->nullable();
            $table->decimal('lng', 10, 7)->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->index(['rider_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('scan_logs');
        Schema::dropIfExists('incidents');
    }
};
