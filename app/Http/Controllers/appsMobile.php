<?php

namespace App\Http\Controllers;

use App\Transformers\DataDepartamento;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\vaciadocba;

class appsMobile extends Controller
{

    public function VerifyActiveDepartmentsMethods(){
        $query = vaciadocba::join('diaco_asignarsedecba','diaco_asignarsedecba.correlativo','=','diaco_vaciadocba.Ncorrelativo')
            ->join()
            ->get();
        return response()->json($query, 200);
//select distinct depa.codigo_departamento as code, depa.nombre_departamento as name from diaco_vaciadocba vaciado
//INNER JOIN diaco_asignarsedecba da on vaciado.Ncorrelativo = da.correlativo
//INNER JOIN diaco_sede sede on da.idSede = sede.id_diaco_sede
//INNER JOIN municipio muni on muni.codigo_municipio = sede.codigo_municipio
//INNER JOIN departamento depa on depa.codigo_departamento = muni.codigo_departamento
//    /*where da.filtro = 4*/
//ORDER BY depa.nombre_departamento;
}
    public function VerifyActiveDepartments(){
        $departments = DB::SELECT('exec VerifyActiveDepartments');
        foreach ($departments as $branch)
        {
            $codeDepart = $branch->code;
            $sedes = DB::SELECT('exec getOfficesByDepartment :department',['department' => $codeDepart]);
            $convert = collect($sedes);
            foreach ($convert as $convertt)
            {
                $data = DB::SELECT('exec getCategoriesForDepartament :sede',['sede' => $convertt->code]);
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
        $brancheData = collect($responseSede);
        foreach ($departments as $department){
            $branches = $department->code;
            $sedes = DB::SELECT('exec getOfficesByDepartment :department',['department' => $branches]);
            $data = $brancheData->where('departamento',$department->code);
            $response[] = [
                'code' => $department->code,
                'name' => $department->name,
                'sedes' => $data
            ];
        }
        return fractal()
            ->collection($response)
            ->transformWith(new DataDepartamento())
            ->includeCharacters()
            ->toArray();
    }

}
