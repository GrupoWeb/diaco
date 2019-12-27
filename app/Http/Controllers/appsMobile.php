<?php

namespace App\Http\Controllers;

use App\Transformers\DataDepartamento;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\vaciadocba;
use App\Models\api\beAssigned;

class appsMobile extends Controller
{

    public function VerifyActiveDepartmentsMethods(){
        $query = vaciadocba::distinct()
            ->select('depa.codigo_departamento as code', 'depa.nombre_departamento as name')
            ->join('diaco_asignarsedecba as asignacion','asignacion.correlativo','=','diaco_vaciadocba.Ncorrelativo')
            ->join('diaco_sede as sede','sede.id_diaco_sede','=','asignacion.idSede')
            ->join('municipio as muni','muni.codigo_municipio','=','sede.codigo_municipio')
            ->join('departamento as depa','depa.codigo_departamento','=','muni.codigo_departamento')
            ->orderBy('depa.nombre_departamento')
            //->where('asignacion.filtro','=',4)
            ->get();
        return $query;
        //return response()->json($query, 200);
}

    public function OfficesByDepartaments($departament){
        $data = vaciadocba::distinct()
                    ->select('sede.id_diaco_sede as code','sede.nombre_sede as name','sede.codigo_municipio as code_depa','depa.codigo_departamento','coordenada.latitut','coordenada.longitud')
                    ->join('diaco_usuario as usuario','usuario.id_usuario','=','diaco_vaciadocba.idVerificador')
                    ->join('diaco_sede as sede','sede.id_diaco_sede','=','usuario.id_sede_diaco')
                    ->join('municipio as muni','muni.codigo_municipio','=','sede.codigo_municipio')
                    ->join('departamento as depa','depa.codigo_departamento','=','muni.codigo_departamento')
                    ->join('diaco_coordenadas_cba as coordenada','coordenada.id_sede','=','sede.id_diaco_sede')
                    ->join('diaco_asignarsedecba as da','diaco_vaciadocba.Ncorrelativo','=','da.correlativo')
                    ->where('depa.codigo_departamento','=', $departament)
                    ->get();

        return $data;

    }

    public function getCategoriesForDepartaments($depatamento){
        $data = vaciadocba::distinct()
            ->select('categoria.id_Categoria as code','categoria.nombre as name')
            ->join('diaco_asignarsedecba as da','da.correlativo','=','diaco_vaciadocba.Ncorrelativo')
            ->join('diaco_sede as sede','sede.id_diaco_sede','=','da.idSede')
            ->join('diaco_name_template_cba as plantilla','plantilla.id','=','da.idPlantilla')
            ->join('diaco_plantillascba as plantillas','plantillas.NombrePlantilla','=','plantilla.NombreTemplate')
            ->join('diaco_categoriacba as categoria','categoria.id_Categoria','=','plantillas.idCategoria')
            ->join('municipio as muni','muni.codigo_municipio','=','sede.codigo_municipio')
            ->join('departamento as depa','depa.codigo_departamento','=','muni.codigo_departamento')
            ->where('sede.id_diaco_sede ','=',$depatamento)
            ->get();

        return $data;
    }
    public function VerifyActiveDepartments(){
        //$departments = DB::SELECT('exec VerifyActiveDepartments');
//        return $departments->each(function($departament,$key){
////            $sedes = $this->OfficesByDepartaments($departament->code);
//            return $key;
//        });

        $departments = $this->VerifyActiveDepartmentsMethods();

        foreach ($departments as $branch)
        {
            $codeDepart = $branch->code;
            $sedes = DB::SELECT('exec getOfficesByDepartment :department',['department' => $codeDepart]);
            //$sedes = $this->OfficesByDepartaments($codeDepart);
            $convert = collect($sedes);
            foreach ($convert as $convertt)
            {
                //$data = DB::SELECT('exec getCategoriesForDepartament :sede',['sede' => $convertt->code]);
                $data = $this->getCategoriesForDepartaments($convertt->code);
                $responseSede[] = [
                    'code' => $convertt->code,
                    'name' => $convertt->name,
                    'latitude' => $convertt->latitut,
                    'longitude' => $convertt->longitud,
                    'departamento' => $convertt->codigo_departamento,
                    'categories' =>$data
                ];
            }
        }
//        $brancheData = collect($responseSede);
//        foreach ($departments as $department){
//            $branches = $department->code;
//            //$sedes = DB::SELECT('exec getOfficesByDepartment :department',['department' => $branches]);
//            //$data = $brancheData->where('departamento',$department->code);
//            $response[] = [
//                'code' => $department->code,
//                'name' => $department->name,
//                //'sedes' => $data
//            ];
//        }



//        return fractal()
//            ->collection($response)
//            ->transformWith(new DataDepartamento())
//            ->includeCharacters()
//            ->toArray();
    }

}
