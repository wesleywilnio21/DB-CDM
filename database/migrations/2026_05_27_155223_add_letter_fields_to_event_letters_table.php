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
            $table->string('letter_number')->nullable()->after('event_id');
            $table->unsignedInteger('letter_sequence')->nullable()->after('letter_number');
            $table->date('issued_at')->nullable()->after('body');
            $table->string('city')->nullable()->after('issued_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('event_letters', function (Blueprint $table) {
            $table->dropColumn(['letter_number', 'letter_sequence', 'issued_at', 'city']);
        });
    }
};
