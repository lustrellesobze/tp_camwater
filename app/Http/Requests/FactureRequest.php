<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class FactureRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'abonne_id' => 'required|exists:abonnes,abonne_id',

            'consommation' => 'required|integer|min:1',

            'statut' => 'in:Emise,Paye'
        ];
    }

    public function messages()
    {
        return [

            'abonne_id.required' => 'L\'abonné est obligatoire.',
            'abonne_id.exists' => 'Cet abonné n\'existe pas.',

            'consommation.required' => 'La consommation est obligatoire.',
            'consommation.integer' => 'La consommation doit être un nombre entier.',
            'consommation.min' => 'La consommation doit être strictement positive.',

            'statut.in' => 'Le statut doit être Emise ou Paye.'

        ];
    }
}