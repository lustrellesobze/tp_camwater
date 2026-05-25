<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Abonne extends Model
{
    //
    protected $table='abonnes';
    protected $primaryKey = 'abonne_id';
    public $incrementing = true;
    protected $fillable = [
        'nom',
        'prenom',
        'ville',
        'quartier',
        'numerocompteur',
        'typeabonnement',
    ];
    public $timestamps = true;
    
        public function factures()
    {
        return $this->hasMany(Facture::class, 'abonne_id');
    }

}
