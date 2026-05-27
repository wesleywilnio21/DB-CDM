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
        Schema::table('event_letters', function (Blueprint $table) {
            $table->foreignId('logo_asset_id')->nullable()->constrained('letter_assets')->nullOnDelete();
            $table->foreignId('kop_asset_id')->nullable()->constrained('letter_assets')->nullOnDelete();
            $table->foreignId('ttd_asset_id')->nullable()->constrained('letter_assets')->nullOnDelete();
            $table->string('sig_text_above')->nullable();
            $table->string('sig_name')->nullable();
            $table->string('sig_position')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('event_letters', function (Blueprint $table) {
            $table->dropForeign(['logo_asset_id']);
            $table->dropForeign(['kop_asset_id']);
            $table->dropForeign(['ttd_asset_id']);
            $table->dropColumn([
                'logo_asset_id',
                'kop_asset_id',
                'ttd_asset_id',
                'sig_text_above',
                'sig_name',
                'sig_position'
            ]);
        });
    }
};
