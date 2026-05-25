<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;

class Utilisateurs extends Model
{
    use HasApiTokens; // <-- Pour gérer les tokens API avec Sanctum

    protected $table = 'utilisateurs';
    protected $primaryKey = 'utilisateur_id';
    public $incrementing = true;

    protected $fillable = [
        'nom',
        'prenom',
        'telephone',
        'email',
        'password',
        'role'
    ];

    public $timestamps = true;

    // Masquer le password lors de la conversion en JSON
    protected $hidden = [
        'password',
    ];


}