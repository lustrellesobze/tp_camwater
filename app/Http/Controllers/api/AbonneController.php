<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Abonne;
use Illuminate\Http\Request;
use App\Http\Requests\AbonneRequest;
use Illuminate\Support\Facades\Log;

class AbonneController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/abonne",
     *     summary="Liste tous les abonnés",
     *     tags={"Abonnés"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Liste paginée des abonnés"
     *     )
     * )
     */
    public function index(Request $request)
    {
        Log::info("Début de l'affichage de la liste des abonnés.");
        $perPage = 15;
        $query = Abonne::query();
        $abonne = $query->orderBy('nom', 'desc')->paginate($perPage);
        Log::info("Liste des abonnés récupérée avec succès. Nombre par page : " . $perPage);
        return response()->json($abonne, 200);
    }

    /**
     * @OA\Post(
     *     path="/api/abonne",
     *     summary="Créer un nouvel abonné",
     *     tags={"Abonnés"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"nom","prenom","ville","quartier","numerocompteur","typeabonnement"},
     *             @OA\Property(property="nom", type="string", example="Dupont"),
     *             @OA\Property(property="prenom", type="string", example="Jean"),
     *             @OA\Property(property="ville", type="string", enum={"Yaounde","Douala","Bafoussam","Garoua"}, example="Yaounde"),
     *             @OA\Property(property="quartier", type="string", example="Bastos"),
     *             @OA\Property(property="numerocompteur", type="string", example="CPT00001"),
     *             @OA\Property(property="typeabonnement", type="string", enum={"Domestique","Professionnel"}, example="Domestique")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Abonné créé avec succès",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Abonné crée avec succès"),
     *             @OA\Property(property="data", type="object")
     *         )
     *     ),
     *     @OA\Response(response=422, description="Données invalides")
     * )
     */
    public function store(AbonneRequest $request)
    {
        Log::info("Tentative de création d'un nouvel abonné.", ['donnees' => $request->validated()]);
        $abonne = Abonne::create($request->validated());
        Log::info("Abonné créé avec succès. ID : " . $abonne->id);
        return response()->json([
            'message' => 'Abonné crée avec succès',
            'data'=> $abonne
        ], 201);
    }

    /**
     * @OA\Get(
     *     path="/api/abonne/{id}",
     *     summary="Afficher un abonné",
     *     tags={"Abonnés"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(response=200, description="Abonné trouvé"),
     *     @OA\Response(response=404, description="Abonné introuvable")
     * )
     */
    public function show($id)
    {
        Log::info("Recherche de l'abonné avec l'ID : " . $id);
        $abonne = Abonne::find($id);
        if (!$abonne) {
            Log::warning("Échec de l'affichage : Abonné introuvable pour l'ID : " . $id);
            return response()->json(['message' => 'Niveau introuvable'], 404);
        }
        Log::info("Abonné trouvé : ", ['id' => $abonne->id, 'nom' => $abonne->nom]);
        return response()->json(['data' => $abonne], 200);
    }

    /**
     * @OA\Put(
     *     path="/api/abonne/{id}",
     *     summary="Mettre à jour un abonné",
     *     tags={"Abonnés"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\RequestBody(
     *         @OA\JsonContent(
     *             @OA\Property(property="nom", type="string", example="Dupont modifié"),
     *             @OA\Property(property="prenom", type="string", example="Jean"),
     *             @OA\Property(property="quartier", type="string", example="Nouveau quartier")
     *         )
     *     ),
     *     @OA\Response(response=200, description="Abonné mis à jour"),
     *     @OA\Response(response=404, description="Abonné introuvable")
     * )
     */
    public function update(Request $request, $id)
    {
        Log::info("Tentative de mise à jour de l'abonné ID : " . $id);
        $abonne = Abonne::find($id);
        if (!$abonne) {
            Log::warning("Échec de la mise à jour : Abonné ID " . $id . " introuvable.");
            return response()->json(['message' => 'abonne introuvable'], 404);
        }
        $validateData = $request->validate([
            'nom' => 'sometimes|string|min:5',
            'prenom' => 'nullable|string',
            'quartier' => 'nullable|string',
            'villle' => 'sometimes|string|exists:filieres,code_filiere'
        ]);
        $abonne->update($validateData);
        Log::info("Abonné ID " . $id . " mis à jour avec succès.");
        return response()->json([
            'message' => 'Niveau mis à jour avec succès',
            'data' => $abonne
        ], 200);
    }

    /**
     * @OA\Delete(
     *     path="/api/abonne/{id}",
     *     summary="Supprimer un abonné",
     *     tags={"Abonnés"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(response=200, description="Abonné supprimé avec succès"),
     *     @OA\Response(response=404, description="Abonné introuvable")
     * )
     */
    public function destroy(Abonne $abonne)
    {
        Log::info("Tentative de suppression de l'abonné ID : " . $abonne->id);
        $abonne->delete();
        Log::info("Abonné ID " . $abonne->id . " supprimé avec succès.");
        return response()->json(['message' => 'Abonné supprimé avec succès'], 200);
    }
}