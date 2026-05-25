<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

// Modèles
use App\Models\Abonne;
use App\Models\Facture;
use App\Models\Operateur;
use App\Models\Reclamation;


class StatsController extends Controller
{
    public function getDashboardStats(Request $request)
    {
       

        try {
            $totalAbonne = Abonne::count(); 
            $totalFacture = Facture::count(); 

            // --- 1. ANALYSE DES Abonnés ---
            Log::info("🔍 [stat abonnés] Calcul des abonnés...");
            $villes = Abonne::select('ville', DB::raw('count(*) as total'))
                ->whereNotNull('ville')
                ->groupBy('ville')
                ->orderByDesc('total')
                ->get()
                ->map(function ($v) use ($totalAbonne) {
                    $v->pourcentage = $totalAbonne > 0 ? round(($v->total / $totalAbonne) * 100, 1) : 0;
                    return $v;
                });

            $abonneStats = [
                'total' => $totalAbonne,
                'villes_detaillees' => $villes
            ];


            
            // --- 2. ANALYSE DES Factures ---
            Log::info("🔍 [stat facture] Calcul des abonnés...");
            $consommations = Facture::select('consommation', DB::raw('count(*) as total'))
                ->whereNotNull('consommation')
                ->groupBy('consommation')
                ->orderByDesc('total')
                ->get()
                ->map(function ($v) use ($totalFacture) {
                    $v->pourcentage = $totalFacture > 0 ? round(($v->total / $totalFacture) * 100, 1) : 0;
                    return $v;
                });

            $factureStats = [
                'total' => $totalFacture,
                'consommation_detaillees==' => $consommations
            ];

            return response()->json([
                'status' => 'success',
                'abonnes' => $abonneStats,
                'factures'=>$factureStats,
                
            ]);

        } catch (\Exception $e) {
            Log::error("❌ [STATS_ERROR] Erreur Critique", ['msg' => $e->getMessage()]);
            return response()->json(['status' => 'error', 'message' => 'Erreur de calcul'], 500);
        }
    }

    
}