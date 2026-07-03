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
        Schema::create('letter_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('letter_template_id')->constrained()->cascadeOnDelete();
            $table->foreignId('contact_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete(); // created by
            $table->string('letter_number')->unique();
            $table->integer('sequence');
            $table->integer('month');
            $table->integer('year');
            $table->timestamps();
            
            $table->unique(['letter_template_id', 'sequence', 'month', 'year'], 'unique_template_monthly_sequence');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('letter_documents');
    }
};
