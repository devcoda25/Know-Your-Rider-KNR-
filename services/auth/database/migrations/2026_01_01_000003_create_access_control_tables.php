<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('roles', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->string('key')->unique();
            $table->string('name');
            $table->text('description')->nullable();
            $table->timestamps();
        });

        Schema::create('permissions', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->string('key')->unique(); // e.g. rider.view_full
            $table->text('description')->nullable();
            $table->timestamps();
        });

        Schema::create('permission_role', function (Blueprint $table) {
            $table->foreignUlid('role_id')->constrained()->cascadeOnDelete();
            $table->foreignUlid('permission_id')->constrained()->cascadeOnDelete();
            $table->primary(['role_id', 'permission_id']);
        });

        // A user + a role + a scope. Example: stage_head of stage X, traffic_officer of division Y.
        Schema::create('role_assignments', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('user_id')->constrained()->cascadeOnDelete();
            $table->foreignUlid('role_id')->constrained()->cascadeOnDelete();
            // platform | district | division | parish | village | stage | organization
            $table->string('scope_type')->nullable();
            $table->string('scope_id')->nullable();
            $table->string('granted_by')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'scope_type', 'scope_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('role_assignments');
        Schema::dropIfExists('permission_role');
        Schema::dropIfExists('permissions');
        Schema::dropIfExists('roles');
    }
};
