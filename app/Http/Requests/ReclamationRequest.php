<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ReclamationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'facture_id' => 'required|exists:factures,facture_id',

            'reponse' => 'required|string|',
        ];
    }

    public function messages()
    {
        return [

            'facture_id' => 'La facture correspondante est obligatoire',
            
            'reponse' => 'La reponse est obligatoire'

        ];
    }
}
