<?php

namespace Tests\Feature;

use Tests\TestCase;
use Tests\Traits\ApiTokenTrait;
use App\Models\Abonne;
use App\Models\Facture;
use Illuminate\Foundation\Testing\RefreshDatabase;

class FactureTest extends TestCase
{
    use ApiTokenTrait, RefreshDatabase;

    protected Abonne $abonne;

    protected function setUp(): void
    {
        parent::setUp();
        $this->authenticateOperateur();

        // Abonné réutilisé dans tous les tests
        $this->abonne = Abonne::create([
            'nom'            => 'TestNom',
            'prenom'         => 'TestPrenom',
            'ville'          => 'Yaounde',
            'quartier'       => 'Bastos',
            'numerocompteur' => 'CPT99001',
            'typeabonnement' => 'Domestique',
        ]);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function generer_facture()
    {
        $response = $this->postJson('/api/admin/facture/generer', [
            'abonne_id'    => $this->abonne->abonne_id,
            'consommation' => 15,
            'statut'       => 'Emise',
        ]);

        $response->assertStatus(201)
                 ->assertJsonStructure([
                     'facture_id',
                     'abonne_id',
                     'consommation',
                     'montant_total',
                     'statut',
                     'abonne' => [
                         'nom',
                         'prenom',
                         'typeabonnement',
                     ]
                 ]);
    }



    #[\PHPUnit\Framework\Attributes\Test]
    public function show_facture()
    {
        $facture = Facture::create([
            'abonne_id'    => $this->abonne->abonne_id,
            'consommation' => 10,
            'montant_total'=> 3500,
            'statut'       => 'Emise',
        ]);

        $response = $this->getJson("/api/admin/facture/{$facture->facture_id}");

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'facture_id',
                     'abonne_id',
                     'consommation',
                     'montant_total',
                     'statut',
                     'abonne',
                 ]);
    }


}