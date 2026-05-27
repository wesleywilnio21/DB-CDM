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
        // 1. Migrate data from donation_histories to donation_session_donors
        if (Schema::hasTable('donation_histories')) {
            $histories = DB::table('donation_histories')->get();
            foreach ($histories as $history) {
                // Find or create session for this date and location
                $session = DB::table('donation_sessions')
                    ->where('session_date', $history->donated_at)
                    ->where('location', $history->location)
                    ->first();
                
                if (!$session) {
                    $sessionId = DB::table('donation_sessions')->insertGetId([
                        'name' => 'Legacy Migration', // temporary name
                        'session_date' => $history->donated_at,
                        'location' => $history->location,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                } else {
                    $sessionId = $session->id;
                }

                // Insert into donation_session_donors
                DB::table('donation_session_donors')->insertOrIgnore([
                    'donation_session_id' => $sessionId,
                    'blood_donor_id' => $history->blood_donor_id,
                    'donated_at' => $history->donated_at,
                    'location' => $history->location,
                    'notes' => $history->notes,
                    'created_at' => $history->created_at ?? now(),
                    'updated_at' => $history->updated_at ?? now(),
                ]);
            }

            // Drop donation_histories
            Schema::dropIfExists('donation_histories');
        }

        // 2. Drop name column from donation_sessions
        Schema::table('donation_sessions', function (Blueprint $table) {
            $table->dropColumn('name');
        });
    }

    public function down(): void
    {
        Schema::create('donation_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('blood_donor_id')->constrained()->cascadeOnDelete();
            $table->date('donated_at');
            $table->string('location')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::table('donation_sessions', function (Blueprint $table) {
            $table->string('name')->after('id')->nullable();
        });
    }
};
