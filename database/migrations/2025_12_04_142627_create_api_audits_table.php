<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('api_audits', function (Blueprint $table) {
            $table->id();

            // Who made the request
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->unsignedBigInteger('token_id')->nullable(); // personal_access_tokens.id

            // Request metadata
            $table->string('method', 10);
            $table->string('path', 255);
            $table->string('route_name', 255)->nullable();
            $table->unsignedSmallInteger('status_code');
            $table->string('ip_address', 45)->nullable();
            $table->string('user_agent', 255)->nullable();

            // Timing
            $table->unsignedInteger('duration_ms')->nullable();

            // Optional payload logging (trimmed for size)
            $table->text('query')->nullable();
            $table->text('request_body')->nullable();

            $table->timestamps();

            $table->index(['user_id', 'created_at']);
            $table->index(['token_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('api_audits');
    }
};
