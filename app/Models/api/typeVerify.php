<?php

namespace App\Models\api;

use Illuminate\Database\Eloquent\Model;

class typeVerify extends Model
{
    public $timestamps = false;
    protected $fillable = [
        'created_at','idPlantilla', 'idVerificador','tipoVerificacion','idLugarVisita','idEstablecimientoVisita','numeroLocal','idProducto','idMedida','precioProducto','estado'
    ];

    protected $table = 'diaco_tipoVerificacioncba';

    public function vaciados(){
        return $this->hasMany('vaciadocba');
    }
}
