<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('qr_codes', function (Blueprint $table) {
            $table->boolean('is_dynamic')->default(false)->after('color_light');
            $table->string('slug')->nullable()->unique()->after('is_dynamic');
            $table->unsignedBigInteger('scan_count')->default(0)->after('slug');
        });
    }

    public function down(): void
    {
        Schema::table('qr_codes', function (Blueprint $table) {
            $table->dropColumn(['is_dynamic', 'slug', 'scan_count']);
        });
    }
};
