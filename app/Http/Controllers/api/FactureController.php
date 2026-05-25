<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Facture;
use App\Models\Abonne;
use App\Http\Requests\FactureRequest;
use Exception;
use Illuminate\Support\Facades\Log;

class FactureController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/facture",
     *     summary="Liste toutes les factures",
     *     tags={"FacturSFM Ves"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(response=200, description="Liste des factures avec leurs abonnés")
     * )
     */
    public function index()
    {
        Log::info("Récupération de la liste de toutes les factures.");
        $factures = Facture::with('abonne')->get();
        Log::info("Nombre de factures récupérées : " . $factures->count());
        return response()->json($factures, 200);
    }

    /**
     * @OA\Get(
     *     path="/api/facture/{id}",
     *     summary="Afficher une facture",
     *     tags={"Factures"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(response=200, description="Facture trouvée avec son abonné"),
     *     @OA\Response(response=404, description="Facture introuvable")
     * )
     */
    public function show(Facture $facture)
    {
        Log::info("Affichage de la facture ID : " . $facture->facture_id);
        $facture->load('abonne');
        return response()->json($facture, 200);
    }

    /**
     * @OA\Post(
     *     path="/api/facture/generer",
     *     summary="Générer une facture automatiquement",
     *     tags={"Factures"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"abonne_id","consommation"},
     *             @OA\Property(property="abonne_id", type="integer", example=1),
     *             @OA\Property(property="consommation", type="integer", example=15),
     *             @OA\Property(property="statut", type="string", enum={"Emise","Payee","Annulee"}, example="Emise")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Facture générée avec succès",
     *         @OA\JsonContent(
     *             @OA\Property(property="facture_id", type="integer"),
     *             @OA\Property(property="abonne_id", type="integer"),
     *             @OA\Property(property="consommation", type="integer"),
     *             @OA\Property(property="montant_total", type="number"),
     *             @OA\Property(property="statut", type="string"),
     *             @OA\Property(property="abonne", type="object")
     *         )
     *     ),
     *     @OA\Response(response=404, description="Abonné introuvable"),
     *     @OA\Response(response=400, description="Erreur de calcul")
     * )
     */
    public function genererFacture(FactureRequest $request)
    {
        Log::info("Tentative de génération de facture pour l'abonné ID : " . $request->abonne_id);
        $abonne = Abonne::find($request->abonne_id);
        if (!$abonne) {
            Log::error("Échec génération facture : Abonné " . $request->abonne_id . " introuvable.");
            return response()->json(['error' => 'Abonné introuvable'], 404);
        }
        try {
            $montant = $this->calculerMontant((int)$request->consommation, $abonne->typeabonnement);
            $facture = Facture::create([
                'abonne_id'     => $abonne->abonne_id,
                'consommation'  => (int)$request->consommation,
                'montant_total' => $montant,
                'statut'        => $request->statut ?? 'Emise',
            ]);
            Log::info("Facture générée avec succès. ID Facture : " . $facture->facture_id . " | Montant : " . $montant);
            return response()->json($facture->load('abonne'), 201);
        } catch (Exception $e) {
            Log::error("Erreur lors de la génération de la facture : " . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    /**
     * @OA\Put(
     *     path="/api/facture/{id}",
     *     summary="Mettre à jour une facture",
     *     tags={"Factures"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\RequestBody(
     *         @OA\JsonContent(
     *             @OA\Property(property="consommation", type="integer", example=20),
     *             @OA\Property(property="statut", type="string", enum={"Emise","Payee","Annulee"}, example="Payee")
     *         )
     *     ),
     *     @OA\Response(response=200, description="Facture mise à jour"),
     *     @OA\Response(response=404, description="Facture introuvable")
     * )
     */
    public function update(FactureRequest $request, Facture $facture)
    {
        Log::info("Mise à jour de la facture ID : " . $facture->facture_id);
        if ($request->has('consommation')) {
            Log::info("Nouvelle consommation détectée : " . $request->consommation);
            $facture->consommation  = (int)$request->consommation;
            $facture->montant_total = $this->calculerMontant($facture->consommation, $facture->abonne->typeabonnement);
        }
        if ($request->has('statut')) {
            Log::info("Changement de statut : " . $facture->statut . " -> " . $request->statut);
            $facture->statut = $request->statut;
        }
        $facture->save();
        Log::info("Facture ID " . $facture->facture_id . " mise à jour avec succès.");
        return response()->json($facture->load('abonne'), 200);
    }

    /**
     * @OA\Delete(
     *     path="/api/facture/{id}",
     *     summary="Supprimer une facture",
     *     tags={"Factures"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(response=200, description="Facture supprimée avec succès"),
     *     @OA\Response(response=404, description="Facture introuvable")
     * )
     */
    public function destroy(Facture $facture)
    {
        Log::info("Suppression de la facture ID : " . $facture->facture_id);
        $facture->delete();
        Log::info("Facture supprimée.");
        return response()->json(['message' => 'Facture supprimée avec succès'], 200);
    }

    public function calculerMontant($consommation, $typeAbonnement)
    {
        Log::debug("Calcul du montant - Consommation : $consommation, Type : $typeAbonnement");
        if (!is_int($consommation) || $consommation <= 0) {
            Log::warning("Calcul avorté : Consommation invalide ($consommation)");
            throw new Exception("La consommation doit être un entier strictement positif.");
        }
        $montant = 0;
        if ($typeAbonnement === 'Domestique') {
            if ($consommation <= 10) {
                $montant = $consommation * 350;
            } elseif ($consommation <= 20) {
                $montant = (10 * 350) + (($consommation - 10) * 550);
            } else {
                $montant = (10 * 350) + (10 * 550) + (($consommation - 20) * 780);
            }
        } elseif ($typeAbonnement === 'Professionnel') {
            $montant = 8500 + ($consommation * 950);
        } else {
            Log::error("Calcul avorté : Type d'abonnement inconnu ($typeAbonnement)");
            throw new Exception("Type d'abonnement invalide.");
        }
        $resultat = ceil($montant);
        Log::debug("Montant calculé : $resultat");
        return $resultat;
    }
}