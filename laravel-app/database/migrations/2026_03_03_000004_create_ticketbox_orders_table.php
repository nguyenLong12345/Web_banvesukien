<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('orders')) {
            return;
        }

        Schema::create('orders', function (Blueprint $table) {
            $table->string('order_id', 10)->primary();
            $table->string('payment_id', 10);
            $table->string('event_id', 10);
            $table->dateTime('created_at');
            $table->integer('quantity');

            // Add indexes for frequently queried columns
            $table->index('created_at');
            $table->index('event_id');
            $table->index('payment_id');

            $table->foreign('event_id', 'fk_event_id')
                ->references('event_id')
                ->on('events')
                ->restrictOnDelete() // Changed to restrict to prevent accidental data loss
                ->cascadeOnUpdate();

            $table->foreign('payment_id', 'fk_payment_id')
                ->references('payment_id')
                ->on('payments')
                ->restrictOnDelete() // Changed to restrict to prevent accidental data loss
                ->cascadeOnUpdate();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};

