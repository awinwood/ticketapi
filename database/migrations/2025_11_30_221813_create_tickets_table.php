<?php

use App\Enums\TicketStatus;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('tickets', function (Blueprint $table) {
            $table->id();
            // Relational fields
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            // Core fields
            $table->string('subject');
            $table->text('content');
            $table->unsignedTinyInteger('status')->default(TicketStatus::OPEN)->index();

            $table->timestamps();

            // Indexes
            $table->index(['status', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tickets');
    }
};
