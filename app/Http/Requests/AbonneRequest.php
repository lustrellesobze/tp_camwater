<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AbonneRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
              
            'nom' => 'required|string|max:50',
            'prenom' => 'required|string|max:50',
            'ville' => 'required|string|max:50',
            'quartier' => 'required|string|max:255',
            'numerocompteur' => 'required|string|max:105',
            'typeabonnement' => 'required|string|max:105',
        ];
        
    }

      public function messages()
    {
        return [
            'nom.required' => 'Veuillez entrer le nom de l\abonné',
            'prenom,.required' => 'Veuillez entrer le prenom de l\abonné',
            'ville.required' => 'Veuillez entrer la ville',
            'quartier.required' => 'Veuillez entrer le quartier',
            'numerocompteur.required' => 'Veuillez entrer le numero de compteur',
            'typeabonnement.required' => 'Veuillez entrer le type d\abonnement',
 
        ];
    }
}
