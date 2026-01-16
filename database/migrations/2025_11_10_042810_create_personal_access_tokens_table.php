<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Jalankan migration.
     */
    public function up(): void
    {
        Schema::create('personal_access_tokens', function (Blueprint $table) {
            $table->id();
            
            // Sesuaikan agar PK string seperti 'K002' bisa disimpan
            $table->string('tokenable_type');
            $table->string('tokenable_id', 191);
            $table->index(['tokenable_type', 'tokenable_id']);

            // Token fields
            $table->string('name');
            $table->string('token', 64)->unique();

            // Sanctum abilities JSON
            $table->text('abilities')->nullable();

            // Tambahan opsional
            $table->timestamp('last_used_at')->nullable();
            $table->timestamp('expires_at')->nullable();

            // Default Laravel timestamps
            $table->timestamps();
        });
    }

    /**
     * Rollback migration.
     */
    public function down(): void
    {
        Schema::dropIfExists('personal_access_tokens');
    }
};
