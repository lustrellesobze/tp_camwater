<?php

namespace Tests\Traits;

use App\Models\Operateur;
use Laravel\Sanctum\Sanctum;

trait ApiTokenTrait
{
    protected function authenticateOperateur(): Operateur
    {
        Operateur::where('login', 'test@test.com')->delete();

        $operateur = Operateur::create([
            'login'    => 'test@test.com',
            'password' => bcrypt('password123'),
            'role'     => 'administrateur',
        ]);

        Sanctum::actingAs($operateur, [], 'sanctum');

        return $operateur;
    }
}