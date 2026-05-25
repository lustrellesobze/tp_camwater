<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class OperateurRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Autoriser toutes les requêtes pour l'instant
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * On peut différencier login et register via la méthode HTTP ou un paramètre.
     */
    public function rules(): array
    {
        if ($this->isMethod('post') && $this->routeIs('auth.register')) {
            // Validation pour l'inscription
            return [
                'login'    => 'required|string|unique:operateurs,login',
                'password' => 'required|string|min:6',
                'role'     => 'nullable|in:operateur,administrateur',
            ];
        }

        if ($this->isMethod('post') && $this->routeIs('auth.login')) {
            // Validation pour la connexion
            return [
                'login'    => 'required|string',
                'password' => 'required|string',
            ];
        }

        return [];
    }

    /**
     * Messages d'erreur personnalisés
     */
    public function messages(): array
    {
        return [
            'login.required'    => 'Le login est obligatoire',
            'login.unique'      => 'Ce login est déjà utilisé',
            'password.required' => 'Le mot de passe est obligatoire',
            'password.min'      => 'Le mot de passe doit contenir au moins 6 caractères',
            'role.in'           => 'Le rôle doit être soit admin soit superadmin',
        ];
    }
}