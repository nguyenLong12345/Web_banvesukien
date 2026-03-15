<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('events')) {
            return;
        }

        Schema::create('events', function (Blueprint $table) {
            $table->string('event_id', 10)->primary();
            $table->string('event_name', 255);
            $table->dateTime('start_time');
            $table->decimal('price', 10, 2);
            $table->string('event_img', 255);
            $table->string('location', 255);
            $table->integer('total_seats');
            $table->enum('event_type', ['music', 'art', 'visit', 'tournament']);
            $table->enum('eStatus', ['Chưa diễn ra', 'Đã kết thúc', 'Đang diễn ra', 'Đã bị hủy'])->default('Chưa diễn ra');
            $table->integer('duration');
            
            // Add indexes for frequently queried columns
            $table->index('eStatus');
            $table->index('event_type');
            $table->index('start_time');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};

