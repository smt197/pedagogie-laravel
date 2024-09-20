<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // First, ensure there are no null values (you can also handle this in a separate migration)
    DB::table('users')->whereNull('photo')->update(['photo' => 'default_photo_url']); // Replace with a valid default value

    Schema::table('users', function (Blueprint $table) {
        $table->text('photo')->nullable(false)->change(); // Set as NOT NULL
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            //
        });
    }
};
