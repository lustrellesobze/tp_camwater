<?php

namespace App\Http\Controllers;

/**
 * @OA\Info(
 *     title="Camwater API",
 *     version="1.0.0",
 *     description="Documentation de l'API Camwater - Gestion des abonnés et factures"
 * )
 *
 * @OA\SecurityScheme(
 *     securityScheme="bearerAuth",
 *     type="http",
 *     scheme="bearer",
 *     bearerFormat="JWT"
 * )
 *
 * @OA\Server(
 *     url="http://localhost:8000",
 *     description="Serveur local"
 * )
 */
abstract class Controller
{
    //
}