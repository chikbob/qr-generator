<?php
// database/migrations/2025_11_07_000003_add_details_to_qr_scans_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('qr_scans', function (Blueprint $table) {
            $table->string('country')->nullable()->after('ip');
            $table->string('city')->nullable()->after('country');
            $table->string('device')->nullable()->after('user_agent');
            $table->string('browser')->nullable()->after('device');
        });
    }

    public function down(): void
    {
        Schema::table('qr_scans', function (Blueprint $table) {
            $table->dropColumn(['country', 'city', 'device', 'browser']);
        });
    }
};
