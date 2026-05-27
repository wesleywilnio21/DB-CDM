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
        Schema::create('donation_session_donors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('donation_session_id')->constrained()->onDelete('cascade');
            $table->foreignId('blood_donor_id')->constrained()->onDelete('cascade');
            $table->date('donated_at');
            $table->string('location')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('donation_session_donors');
    }
};
