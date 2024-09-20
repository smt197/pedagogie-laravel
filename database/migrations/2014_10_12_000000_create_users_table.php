<?php

use App\Enums\ActiveEnum;
use App\Enums\StatutEnum;
use Illuminate\Database\DBAL\TimestampType;
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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
           /* $table->string('refresh_token')->nullable();*/
            $table->string('nom');
            $table->string('prenom');
            $table->string('photo');
            $table->string('login')->unique();
            $table->string('password');
            $table->string('fonction');
            $table->enum('statut', array_column(StatutEnum::cases(), 'value'))->default(StatutEnum::ACTIF->value);
            $table->unsignedBigInteger('role_id');
            $table->foreign('role_id')->references('id')->on('roles')->onDelete('set null');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
