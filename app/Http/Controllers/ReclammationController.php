<?php

namespace App\Http\Controllers;

use App\Models\Reclamation;
use Illuminate\Http\Request;

class ReclammationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validateData = $request->validate([           
            'facture_id' => 'required|exists:factures,facture_id',
        ]);

        $reclamation = Reclamation::create($validateData);

      

        return response()->json([
            'message' => 'reclammation créée avec succès',
            'data' => $reclamation
        ], 201);

    }

    /**
     * Display the specified resource.
     */
    public function show(Reclamation $reclamation)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $reclamation = Reclamation::find($id);


        $validateData = $request->validate([
            'reponse' => 'required|string',
        ]);

        $reclamation->update($validateData);


        return response()->json([
            'message' => 'reclamation mise à jour avec succès',
            'data' => $validateData
        ], 200);
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Reclamation $reclamation)
    {
        //
    }
}
