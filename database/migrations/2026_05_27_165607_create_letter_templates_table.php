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
        Schema::create('letter_templates', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('body');

            // Assets (nullable)
            $table->foreignId('logo_asset_id')->nullable()->constrained('letter_assets')->nullOnDelete();
            $table->foreignId('kop_asset_id')->nullable()->constrained('letter_assets')->nullOnDelete();
            $table->foreignId('ttd_asset_id')->nullable()->constrained('letter_assets')->nullOnDelete();

            // Signature details
            $table->string('sig_text_above')->nullable(); // e.g. "Hormat Kami,"
            $table->string('sig_name')->nullable();       // e.g. "John Doe"
            $table->string('sig_position')->nullable();   // e.g. "Ketua Panitia"

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('letter_templates');
    }
};
