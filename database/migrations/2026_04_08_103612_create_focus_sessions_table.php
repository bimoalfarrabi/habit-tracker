<?php

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
        Schema::create('focus_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('habit_id')->nullable()->constrained()->nullOnDelete();
            $table->date('session_date');
            $table->timestamp('start_time');
            $table->timestamp('end_time')->nullable();
            $table->unsignedInteger('planned_duration_minutes')->nullable();
            $table->unsignedInteger('total_duration_seconds')->default(0);
            $table->unsignedInteger('focused_duration_seconds')->default(0);
            $table->unsignedInteger('unfocused_duration_seconds')->default(0);
            $table->unsignedInteger('interruption_count')->default(0);
            $table->string('status', 20)->default('running');
            $table->text('note')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'session_date']);
            $table->index('habit_id');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('focus_sessions');
    }
};
