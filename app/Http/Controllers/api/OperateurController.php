<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use App\Models\Operateur;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\OperateurRequest;

class OperateurController extends Controller
{
    public function login(OperateurRequest $request)
    {
        Log::info("═══════════════════════════════════════");
        Log::info(">>> LOGIN DÉMARRÉ");
        Log::info("═══════════════════════════════════════");
        Log::info("Données brutes reçues", [
            'login'        => $request->input('login'),
            'password_len' => strlen($request->input('password', '')),
            'ip'           => $request->ip(),
            'method'       => $request->method(),
            'content_type' => $request->header('Content-Type'),
        ]);

        try {
            Log::info("STEP 1 — Validation des données...");
            $credentials = $request->validated();
            Log::info("STEP 1 — OK : données validées", ['login' => $credentials['login']]);

            Log::info("STEP 2 — Recherche de l'opérateur en base...");
            $user = Operateur::where('login', $credentials['login'])->first();

            if (!$user) {
                Log::warning("STEP 2 — ÉCHEC : aucun opérateur trouvé pour le login", [
                    'login' => $credentials['login']
                ]);
                return response()->json(['message' => 'Identifiants invalides'], 401);
            }

            Log::info("STEP 2 — OK : opérateur trouvé", [
                'operateur_id' => $user->operateur_id,
                'login'        => $user->login,
                'role'         => $user->role,
            ]);

            Log::info("STEP 3 — Vérification du mot de passe...");
            $passwordOk = Hash::check($credentials['password'], $user->password);
            Log::info("STEP 3 — Résultat Hash::check", ['match' => $passwordOk]);

            if (!$passwordOk) {
                Log::warning("STEP 3 — ÉCHEC : mot de passe incorrect", [
                    'login' => $credentials['login']
                ]);
                return response()->json(['message' => 'Identifiants invalides'], 401);
            }

            Log::info("STEP 4 — Suppression des anciens tokens...");
            $deleted = $user->tokens()->delete();
            Log::info("STEP 4 — OK : tokens supprimés", ['nb_deleted' => $deleted]);

            Log::info("STEP 5 — Vérification table personal_access_tokens...");
            try {
                $tableExists = DB::getSchemaBuilder()->hasTable('personal_access_tokens');
                Log::info("STEP 5 — Table personal_access_tokens existe ?", ['exists' => $tableExists]);

                $tokenCount = DB::table('personal_access_tokens')
                    ->where('tokenable_type', Operateur::class)
                    ->where('tokenable_id', $user->operateur_id)
                    ->count();
                Log::info("STEP 5 — Tokens actuels en base pour cet opérateur", ['count' => $tokenCount]);
            } catch (\Throwable $dbEx) {
                Log::error("STEP 5 — ERREUR accès table tokens", ['error' => $dbEx->getMessage()]);
            }

            Log::info("STEP 6 — Création du nouveau token...");
            try {
                $newToken = $user->createToken('auth_token');
                Log::info("STEP 6 — createToken() OK", [
                    'token_object_class' => get_class($newToken),
                    'plain_text_token'   => !empty($newToken->plainTextToken) ? 'NON VIDE ✅' : 'VIDE ❌',
                    'token_id'           => $newToken->accessToken->id ?? 'N/A',
                ]);
                $token = $newToken->plainTextToken;
            } catch (\Throwable $tokenEx) {
                Log::error("STEP 6 — ERREUR lors de createToken()", [
                    'error' => $tokenEx->getMessage(),
                    'trace' => $tokenEx->getTraceAsString()
                ]);
                return response()->json(['message' => 'Erreur génération token', 'error' => $tokenEx->getMessage()], 500);
            }

            Log::info("STEP 7 — Vérification token après création en base...");
            try {
                $tokenInDb = DB::table('personal_access_tokens')
                    ->where('tokenable_id', $user->operateur_id)
                    ->latest('created_at')
                    ->first();
                Log::info("STEP 7 — Token en base", [
                    'found'      => !is_null($tokenInDb),
                    'token_name' => $tokenInDb->name ?? 'N/A',
                    'created_at' => $tokenInDb->created_at ?? 'N/A',
                ]);
            } catch (\Throwable $dbEx) {
                Log::error("STEP 7 — ERREUR vérification token en base", ['error' => $dbEx->getMessage()]);
            }

            Log::info("STEP 8 — Construction de la réponse finale...");
            $response = [
                "user"         => $user,
                'access_token' => $token,
                'token_type'   => 'Bearer',
            ];
            Log::info("STEP 8 — Réponse construite", [
                'has_user'         => isset($response['user']),
                'has_access_token' => !empty($response['access_token']),
                'token_preview'    => substr($token, 0, 20) . '...',
            ]);

            Log::info(">>> LOGIN TERMINÉ AVEC SUCCÈS ✅");
            Log::info("═══════════════════════════════════════");

            return response()->json($response, 200);

        } catch (\Throwable $th) {
            Log::error(">>> LOGIN ERREUR CRITIQUE ❌", [
                'message' => $th->getMessage(),
                'file'    => $th->getFile(),
                'line'    => $th->getLine(),
                'trace'   => $th->getTraceAsString()
            ]);
            return response()->json([
                'message' => 'Erreur lors de la connexion',
                'error'   => $th->getMessage()
            ], 500);
        }
    }

    public function register(OperateurRequest $request)
    {
        Log::info("═══════════════════════════════════════");
        Log::info(">>> REGISTER DÉMARRÉ");
        Log::info("═══════════════════════════════════════");

        try {
            $data = $request->validated();
            Log::info("STEP 1 — Données validées", ['login' => $data['login'], 'role' => $data['role'] ?? 'admin']);

            $data['password'] = Hash::make($data['password']);
            Log::info("STEP 2 — Mot de passe hashé");

            $operateur = Operateur::create($data);
            Log::info("STEP 3 — Opérateur créé ✅", [
                'operateur_id' => $operateur->operateur_id,
                'login'        => $operateur->login,
                'role'         => $operateur->role,
            ]);

            Log::info(">>> REGISTER TERMINÉ AVEC SUCCÈS ✅");
            Log::info("═══════════════════════════════════════");

            return response()->json([
                'message'   => 'Opérateur créé avec succès',
                'operateur' => $operateur
            ], 201);

        } catch (\Throwable $th) {
            Log::error(">>> REGISTER ERREUR CRITIQUE ❌", [
                'message' => $th->getMessage(),
                'file'    => $th->getFile(),
                'line'    => $th->getLine(),
                'trace'   => $th->getTraceAsString()
            ]);
            return response()->json([
                'message' => 'Erreur lors de la création de l\'opérateur',
                'error'   => $th->getMessage()
            ], 500);
        }
    }

    public function logout(Request $request)
    {
        Log::info("═══════════════════════════════════════");
        Log::info(">>> LOGOUT DÉMARRÉ");

        try {
            $operateur = $request->user();

            if (!$operateur) {
                Log::warning(">>> LOGOUT ÉCHEC : aucun utilisateur authentifié ❌");
                return response()->json(['message' => 'Non authentifié'], 401);
            }

            Log::info("Opérateur identifié", [
                'operateur_id' => $operateur->operateur_id,
                'login'        => $operateur->login
            ]);

            $operateur->currentAccessToken()->delete();
            Log::info(">>> LOGOUT RÉUSSI ✅");
            Log::info("═══════════════════════════════════════");

            return response()->json(['message' => 'Déconnexion réussie'], 200);

        } catch (\Throwable $th) {
            Log::error(">>> LOGOUT ERREUR CRITIQUE ❌", [
                'message' => $th->getMessage(),
                'file'    => $th->getFile(),
                'line'    => $th->getLine(),
            ]);
            return response()->json([
                'message' => 'Erreur lors de la déconnexion',
                'error'   => $th->getMessage()
            ], 500);
        }
    }
}