<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Operateur;
use Illuminate\Foundation\Testing\RefreshDatabase;

class OperateurTest extends TestCase
{
    use RefreshDatabase;

    #[\PHPUnit\Framework\Attributes\Test]
    public function register_operateur()
    {
        $response = $this->postJson('/api/auth/register', [
            'login'    => 'nouveau@test.com',
            'password' => 'password123',
            'role'     => 'administrateur',
        ]);

        $response->assertStatus(201)
                 ->assertJsonStructure([
                     'message',
                     'operateur' => ['login', 'role'],
                 ]);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function login_operateur()
    {
        Operateur::create([
            'login'    => 'login@test.com',
            'password' => bcrypt('password123'),
            'role'     => 'administrateur',
        ]);

        $response = $this->postJson('/api/auth/login', [
            'login'    => 'login@test.com',
            'password' => 'password123',
        ]);

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'user',
                     'access_token',
                     'token_type',
                 ]);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function login_mauvais_identifiants()
    {
        $response = $this->postJson('/api/auth/login', [
            'login'    => 'inexistant@test.com',
            'password' => 'mauvais',
        ]);

        $response->assertStatus(401)
                 ->assertJson(['message' => 'Identifiants invalides']);
    }
}