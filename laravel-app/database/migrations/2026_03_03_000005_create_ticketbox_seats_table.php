<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('seats')) {
            return;
        }

        Schema::create('seats', function (Blueprint $table) {
            $table->string('seat_id', 10)->primary();
            $table->string('event_id', 10);
            $table->string('seat_type', 10);
            $table->string('seat_number', 10);
            $table->enum('sStatus', ['Đã đặt', 'Còn trống'])->default('Còn trống');
            $table->float('seat_price');

            // Add indexes for frequently queried columns
            $table->index('seat_type');
            $table->index('sStatus');
            $table->index(['event_id', 'sStatus']); // Composite index for common queries

            $table->foreign('event_id', 'seats_event_id_foreign')
                ->references('event_id')
                ->on('events')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('seats');
    }
};

