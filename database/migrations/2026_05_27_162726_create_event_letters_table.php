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
        Schema::create('event_letters', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained()->cascadeOnDelete();
            $table->string('letter_number')->nullable();
            $table->unsignedInteger('letter_sequence')->nullable();
            $table->string('title');
            $table->string('recipient_name');
            $table->string('recipient_phone')->nullable();
            $table->longText('body');
            $table->date('issued_at')->nullable();
            $table->string('city')->nullable();

            // Assets (nullable)
            $table->foreignId('logo_asset_id')->nullable()->constrained('letter_assets')->nullOnDelete();
            $table->foreignId('kop_asset_id')->nullable()->constrained('letter_assets')->nullOnDelete();
            $table->foreignId('ttd_asset_id')->nullable()->constrained('letter_assets')->nullOnDelete();
            $table->string('signature_path')->nullable();

            // Signature details
            $table->string('sig_text_above')->nullable();
            $table->string('sig_name')->nullable();
            $table->string('sig_position')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('event_letters');
    }
};
