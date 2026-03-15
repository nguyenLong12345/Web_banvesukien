<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->text('meta_seats')->nullable()->after('vnp_transaction_no');
            $table->string('meta_event_id')->nullable()->after('meta_seats');
        });
    }

    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropColumn(['meta_seats', 'meta_event_id']);
        });
    }
};
