<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Facture extends Model
{

    protected $table = 'factures';

    protected $primaryKey = 'facture_id';

    public $incrementing = true;

    protected $fillable = [
        'abonne_id',
        'consommation',
        'montant_total',
        'dateEmission',
        'statut',
    ];

    public $timestamps = true;

    public function abonne()
    {
        return $this->belongsTo(Abonne::class, 'abonne_id');
    }

}