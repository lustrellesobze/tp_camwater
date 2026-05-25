<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Reclamation extends Model
{
    protected $table='reclammations';
    protected $primaryKey = 'reclammation_id';
    public $incrementing = true;
    protected $fillable = [
        'facture_id',
        'reponse',
    ];
    public $timestamps = true;
    
 
}
