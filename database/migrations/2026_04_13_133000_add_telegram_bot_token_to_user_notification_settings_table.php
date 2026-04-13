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
        Schema::table('user_notification_settings', function (Blueprint $table): void {
            $table->text('telegram_bot_token')->nullable()->after('telegram_chat_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_notification_settings', function (Blueprint $table): void {
            $table->dropColumn('telegram_bot_token');
        });
    }
};
