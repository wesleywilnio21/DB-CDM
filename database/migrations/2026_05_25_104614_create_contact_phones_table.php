<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('contact_phones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('contact_id')->constrained()->cascadeOnDelete();
            $table->string('phone');
            $table->boolean('is_primary')->default(false);
            $table->timestamps();
        });

        // Migrate existing phones
        $contacts = DB::table('contacts')->whereNotNull('phone')->get();
        $phones = [];
        foreach ($contacts as $contact) {
            if (!empty($contact->phone)) {
                $phones[] = [
                    'contact_id' => $contact->id,
                    'phone' => $contact->phone,
                    'is_primary' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }
        
        if (count($phones) > 0) {
            DB::table('contact_phones')->insert($phones);
        }

        Schema::table('contacts', function (Blueprint $table) {
            $table->dropUnique('contacts_phone_unique');
            $table->dropColumn('phone');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('contacts', function (Blueprint $table) {
            $table->string('phone')->nullable()->after('name');
        });

        // Migrate back
        $primaryPhones = DB::table('contact_phones')->where('is_primary', true)->get();
        foreach ($primaryPhones as $phone) {
            DB::table('contacts')
                ->where('id', $phone->contact_id)
                ->update(['phone' => $phone->phone]);
        }

        Schema::dropIfExists('contact_phones');
    }
};
