<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('tickets')) {
            return;
        }

        Schema::create('tickets', function (Blueprint $table) {
            $table->string('ticket_id', 10)->primary();
            $table->string('order_id', 10);
            $table->string('seat_id', 10);
            $table->enum('tStatus', ['Thành công', 'Đã hủy'])->default('Thành công');

            // Add indexes for frequently queried columns
            $table->index('tStatus');
            $table->index('order_id');
            $table->index('seat_id');

            $table->foreign('order_id', 'fk_order')
                ->references('order_id')
                ->on('orders')
                ->restrictOnDelete(); // Changed to restrict to prevent accidental data loss

            $table->foreign('seat_id', 'fk_seat')
                ->references('seat_id')
                ->on('seats')
                ->restrictOnDelete(); // Changed to restrict to prevent accidental data loss
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tickets');
    }
};

