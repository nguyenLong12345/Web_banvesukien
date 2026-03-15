<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('payments')) {
            return;
        }

        Schema::create('payments', function (Blueprint $table) {
            $table->string('payment_id', 10)->primary();
            $table->string('user_id', 20);
            $table->timestamp('payment_at')->useCurrent();
            $table->enum('method', ['vnpay'])->default('vnpay');
            $table->decimal('amount', 10, 2);
            $table->string('fullname', 50);
            $table->string('email', 50);
            $table->string('phone', 20);
            $table->enum('pStatus', ['paid', 'pending', 'cancel'])->default('pending');
            $table->string('vnp_transaction_no', 50);
            $table->dateTime('payment_time')->nullable();

            // Add indexes for frequently queried columns
            $table->index('pStatus');
            $table->index('payment_at');
            $table->index('payment_time');
            $table->index('vnp_transaction_no');

            $table->foreign('user_id', 'payments_user_id_foreign')
                ->references('user_id')
                ->on('users')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};

