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
        Schema::create('factures', function (Blueprint $table) {
            $table->id('facture_id');
            $table->unsignedBigInteger('abonne_id');
            $table->foreign('abonne_id')->references('abonne_id')->on('abonnes')->onDelete('cascade');
            $table->integer('consommation');
            $table->decimal('montant_total', 10, 2);
            $table->timestamp('dateEmission')->useCurrent();    
            $table->enum('statut',['Emise','Paye'])->default('Emise');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('factures');
    }
};
