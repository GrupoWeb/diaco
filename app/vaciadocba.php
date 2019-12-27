<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class vaciadocba extends Model
{
    public $timestamps = false;
    protected $fillable = [
        'created_at','idPlantilla', 'idVerificador','tipoVerificacion','idLugarVisita','idEstablecimientoVisita','numeroLocal','idProducto','idMedida','precioProducto','estado'
    ];

    protected $table = 'diaco_vaciadocba';

    public function tipo(){
        return $this->belongsTo('App\Models\api\typeVerify',
                                'tipoVerificacion',
                                  'id_TipoVerificacion',
                                  'correlativo');
    }

    public function asignaciones()
    {
        return $this->belongsTo(
            'App\Models\api\beAssigned',
            'Ncorrelativo',
            'correlativo',
            'correlativo'
        );
    }

}
