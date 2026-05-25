<?php

namespace Tests\Feature;

use Tests\TestCase;
use Tests\Traits\ApiTokenTrait;
use App\Models\Abonne;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AbonneTest extends TestCase
{
    use ApiTokenTrait, RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->authenticateOperateur();
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function create_abonne()
    {
        $response = $this->postJson('/api/admin/abonne', [
            'nom'            => 'Dupont',
            'prenom'         => 'Jean',
            'ville'          => 'Yaounde',
            'quartier'       => 'Bastos',
            'numerocompteur' => 'CPT000001',
            'typeabonnement' => 'Domestique',
        ]);

        $response->assertStatus(201)
                 ->assertJsonStructure([
                     'message',
                     'data' => [
                         'nom',
                         'prenom',
                         'ville',
                         'quartier',
                         'numerocompteur',
                         'typeabonnement',
                     ]
                 ]);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function index_abonne()
    {
        Abonne::create([
            'nom'            => 'Martin',
            'prenom'         => 'Paul',
            'ville'          => 'Douala',
            'quartier'       => 'Akwa',
            'numerocompteur' => 'CPT00002',
            'typeabonnement' => 'Professionnel',
        ]);

        $response = $this->getJson('/api/admin/abonne');

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'data',
                     'current_page',
                     'total',
                 ]);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function show_abonne()
    {
        $abonne = Abonne::create([
            'nom'            => 'Martin',
            'prenom'         => 'Paul',
            'ville'          => 'Douala',
            'quartier'       => 'Akwa',
            'numerocompteur' => 'CPT00003',
            'typeabonnement' => 'Professionnel',
        ]);

        $response = $this->getJson("/api/admin/abonne/{$abonne->abonne_id}");

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'data' => [
                         'nom',
                         'prenom',
                         'ville',
                         'quartier',
                         'numerocompteur',
                         'typeabonnement',
                     ]
                 ]);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function update_abonne()
    {
        $abonne = Abonne::create([
            'nom'            => 'Totolong',
            'prenom'         => 'Luc',
            'ville'          => 'Garoua',
            'quartier'       => 'Centre',
            'numerocompteur' => 'CPT00004',
            'typeabonnement' => 'Domestique',
        ]);

        $response = $this->putJson("/api/admin/abonne/{$abonne->abonne_id}", [
            'nom'     => 'Totolong modifié',
            'prenom'  => 'Luc',
            'quartier'=> 'Nouveau quartier',
        ]);

        $response->assertStatus(200)
                 ->assertJsonFragment([
                     'message' => 'Niveau mis à jour avec succès',
                 ]);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function delete_abonne()
    {
        $abonne = Abonne::create([
            'nom'            => 'Delete',
            'prenom'         => 'Moi',
            'ville'          => 'Bafoussam',
            'quartier'       => 'Marché',
            'numerocompteur' => 'CPT00005',
            'typeabonnement' => 'Domestique',
        ]);

        $response = $this->deleteJson("/api/admin/abonne/{$abonne->abonne_id}");

        $response->assertStatus(200)
                 ->assertJson(['message' => 'Abonné supprimé avec succès']);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function show_abonne_introuvable()
    {
        $response = $this->getJson('/api/admin/abonne/99999');

        $response->assertStatus(404)
                 ->assertJson(['message' => 'Niveau introuvable']);
    }
}