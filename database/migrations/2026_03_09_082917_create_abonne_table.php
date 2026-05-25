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
        Schema::create('abonnes', function (Blueprint $table) {
            $table->id('abonne_id');
            $table->string('nom',100)->NotNull();
            $table->string('prenom',50)->NotNull();
            $table->enum('ville',['Yaounde','Douala','Bafoussam','Garoua']);
            $table->string('quartier');
            $table->string('numerocompteur',25)->unique()->NotNull();
            $table->enum('typeabonnement',['Domestique','Professionnel'])->default('Domestique');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('abonne');
    }
};
