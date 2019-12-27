<?php

namespace App\Models\api;

use Illuminate\Database\Eloquent\Model;

class beAssigned extends Model
{
    public $timestamps = false;
    protected $fillable = [
        'idPlantilla','filtro'
    ];

    protected $table = 'diaco_asignarsedecba';

    public function vaciados(){
        return $this->hasMany('App\vaciadocba');
    }
}
