<?php

namespace App\Http\Controllers;

use http\Client\Response;
use Illuminate\Http\Request;
use App\Models\Departamento;
use App\Models\Municipio;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Transformers\PricesData;
use App\Transformers\DataDepartamento;
use League\Fractal;
use App\User;
// use League\Fractal\Resource\Collection;
use App\Models\pricesModel;
use Illuminate\Support\Collection;

class ServiciosRest extends Controller
{
    public function ApiRest(){
        $departamentos = Departamento::all();
        // $municipio = Municipio::all();
        $array_departamentos = [];
        foreach($departamentos as $key => $departamento){
            array_push($array_departamentos,
                [
                    [
                        "code:" => $departamento->codigo_departamento,
                        "name:" => $departamento->nombre_departamento,
                        "locations" => $departamento->municipio()->select('codigo_municipio as code','nombre_municipio as name')->get()
                    ]
                ]);
        }
        return response()->json($array_departamentos, 200);
    }

    public function getSede()
    {
        $departamentos = Departamento::with('sede')
            ->whereHas('sede')
            ->get();
        $cate = DB::select("SELECT distinct categoria.id_Categoria as code, categoria.nombre as name FROM diaco_plantillascba plantilla
		                            INNER JOIN diaco_categoriacba categoria
		                                    ON categoria.id_Categoria = plantilla.idCategoria
	                                WHERE plantilla.NombrePlantilla = (SELECT NombreTemplate FROM diaco_name_template_cba
								                WHERE id = (SELECT distinct idPlantilla FROM diaco_vaciadocba 
												WHERE idPlantilla = (SELECT DISTINCT idPlantilla FROM diaco_vaciadocba))) ");
        $array_departamentos = [];
        foreach ($departamentos as $departamento) {
        	$sedes = $departamento->sede;
                array_push($array_departamentos,
                [
                    [
                        "code" => $departamento->codigo_departamento,
                        "name" => $departamento->nombre_departamento,
                        "branch" => $sedes,
                        "category" => $cate
                    ]
                ]);

        }
        return response()->json($array_departamentos, 200);
    }


    // function __construct( )
    // {
    //     $this->fractal = new Fractal\Manager();
    //     $this->dataTransformer = new PricesData;
    // }

    public function getPriceNivel2($id,$idCatetoria){
        $date = Carbon::now('America/Guatemala');
        $date->toDateTimeString();

        $date_last = $date->addDay(1);

        $last_price = DB::table('diaco_vaciadocba')
                        ->distinct()
                        ->join('diaco_productocba','diaco_productocba.id_producto','=','diaco_vaciadocba.idProducto')
                        ->join('diaco_medida','diaco_medida.id_medida','=','diaco_vaciadocba.idMedida')
                        ->join('diaco_usuario','diaco_usuario.id_usuario','=','diaco_vaciadocba.idVerificador')
                        ->join('diaco_sede','diaco_sede.id_diaco_sede','=','diaco_usuario.id_sede_diaco')
                        ->join('diaco_plantillascba','diaco_plantillascba.idProducto','=','diaco_vaciadocba.idProducto')
                        ->join('diaco_categoriacba','diaco_categoriacba.id_Categoria','=','diaco_plantillascba.idCategoria')
                        // ->selectraw('diaco_medida.id_medida as idMedida, diaco_productocba.id_producto as code,diaco_productocba.nombre as articulo,diaco_medida.nombre as medida,getdate() as fecha_Actual,avg(diaco_vaciadocba.precioProducto) as price')
                        ->selectraw('diaco_productocba.id_producto as code,diaco_productocba.nombre as articulo')
                        ->where('diaco_vaciadocba.created_at','<=', $date_last)
                        ->where('diaco_categoriacba.id_Categoria','=',$idCatetoria)
                        ->where('diaco_sede.id_diaco_sede','=',$id)
                        ->groupBy('diaco_productocba.nombre','diaco_productocba.id_producto')
                        ->orderByRaw('diaco_productocba.id_producto')
                        ->get();

        // return response()->json($last_price, 200);
        return $last_price;
    }

    public function getPriceLast($id,$idCatetoria){
        $date = Carbon::now('America/Guatemala');
        $date->toDateTimeString();


        $date_previous = $date->subHours(3);

        $previous_price = DB::table('diaco_vaciadocba')
                            ->join('diaco_productocba','diaco_productocba.id_producto','=','diaco_vaciadocba.idProducto')
                            ->join('diaco_medida','diaco_medida.id_medida','=','diaco_vaciadocba.idMedida')
                            ->join('diaco_usuario','diaco_usuario.id_usuario','=','diaco_vaciadocba.idVerificador')
                            ->join('diaco_sede','diaco_sede.id_diaco_sede','=','diaco_usuario.id_sede_diaco')
                            ->join('diaco_plantillascba','diaco_plantillascba.idProducto','=','diaco_vaciadocba.idProducto')
                            ->join('diaco_categoriacba','diaco_categoriacba.id_Categoria','=','diaco_plantillascba.idCategoria')
                            // ->selectraw('diaco_productocba.id_producto as code,DATEADD(hour,-3,getdate()) as fecha_Actual,avg(diaco_vaciadocba.precioProducto) as price')
                            ->selectraw('diaco_medida.id_medida as idMedida, diaco_productocba.id_producto as code,diaco_productocba.nombre as articulo,diaco_medida.nombre as medida,getdate() as fecha_Actual,avg(diaco_vaciadocba.precioProducto) as price2')
                            ->where('diaco_vaciadocba.created_at','<=', $date_previous)
                            ->where('diaco_categoriacba.id_Categoria','=',$idCatetoria)
                            ->where('diaco_vaciadocba.precioProducto','>',0)
                            ->where('diaco_sede.id_diaco_sede','=',$id)
                            // ->groupBy('diaco_productocba.nombre','diaco_medida.nombre','diaco_productocba.id_producto')
                            ->groupBy('diaco_productocba.nombre','diaco_medida.nombre','diaco_productocba.id_producto','diaco_medida.id_medida')
                            ->orderByRaw('diaco_productocba.id_producto');
                            // ->get();

        $date_last = $date->addDay(1);

        $last_price = DB::table('diaco_vaciadocba')
                        ->join('diaco_productocba','diaco_productocba.id_producto','=','diaco_vaciadocba.idProducto')
                        ->join('diaco_medida','diaco_medida.id_medida','=','diaco_vaciadocba.idMedida')
                        ->join('diaco_usuario','diaco_usuario.id_usuario','=','diaco_vaciadocba.idVerificador')
                        ->join('diaco_sede','diaco_sede.id_diaco_sede','=','diaco_usuario.id_sede_diaco')
                        ->join('diaco_plantillascba','diaco_plantillascba.idProducto','=','diaco_vaciadocba.idProducto')
                        ->join('diaco_categoriacba','diaco_categoriacba.id_Categoria','=','diaco_plantillascba.idCategoria')
                        ->selectraw('diaco_medida.id_medida as idMedida, diaco_productocba.id_producto as code,diaco_productocba.nombre as articulo,diaco_medida.nombre as medida,getdate() as fecha_Actual,avg(diaco_vaciadocba.precioProducto) as price')
                        ->where('diaco_vaciadocba.created_at','<=', $date_last)
                        ->where('diaco_categoriacba.id_Categoria','=',$idCatetoria)
                        ->where('diaco_sede.id_diaco_sede','=',$id)
                        ->groupBy('diaco_productocba.nombre','diaco_medida.nombre','diaco_productocba.id_producto','diaco_medida.id_medida')
                        // ->orderByRaw('diaco_productocba.id_producto')
                        ->union($previous_price)
                        ->get();




        // return response()->json($last_price, 200);
        return $last_price;
    }
    public function getPricePrevious($id,$idCatetoria){
        $date = Carbon::now('America/Guatemala');
        $date->toDateTimeString();
        $date_previous = $date->subHours(3);


        $previous_price = DB::table('diaco_vaciadocba')
                        ->join('diaco_productocba','diaco_productocba.id_producto','=','diaco_vaciadocba.idProducto')
                        ->join('diaco_medida','diaco_medida.id_medida','=','diaco_vaciadocba.idMedida')
                        ->join('diaco_usuario','diaco_usuario.id_usuario','=','diaco_vaciadocba.idVerificador')
                        ->join('diaco_sede','diaco_sede.id_diaco_sede','=','diaco_usuario.id_sede_diaco')
                        ->join('diaco_plantillascba','diaco_plantillascba.idProducto','=','diaco_vaciadocba.idProducto')
                        ->join('diaco_categoriacba','diaco_categoriacba.id_Categoria','=','diaco_plantillascba.idCategoria')
                        ->selectraw('diaco_productocba.id_producto as code,DATEADD(hour,-3,getdate()) as fecha_Actual,avg(diaco_vaciadocba.precioProducto) as price')
                        ->where('diaco_vaciadocba.created_at','<=', $date_previous)
                        ->where('diaco_categoriacba.id_Categoria','=',$idCatetoria)
                        ->where('diaco_vaciadocba.precioProducto','>',0)
                        ->where('diaco_sede.id_diaco_sede','=',$id)
                        ->groupBy('diaco_productocba.nombre','diaco_medida.nombre','diaco_productocba.id_producto')
                        ->orderByRaw('diaco_productocba.id_producto')
                        ->get();

        // return response()->json($previous_price, 200);
        return $previous_price;
    }

    public function getPriceLastPrevious($id,$idCatetoria){

        $date = Carbon::now('America/Guatemala');
        $date->toDateTimeString();


        $date_previous = $date->subDay(1)->format('Y-m-d');
        $date_last = $date->addDay(1)->format('Y-m-d');

        $price = DB::select("SELECT  
                                    t1.code as code,
                                    t1.idMedida,
                                    t1.medida,
                                    t1.fecha_actual,
                                    t1.Precio_actual,
                                    t2.fecha_actual as fecha_anterior,
                                    t2.Precio_actual2 as precio_anterior
                        FROM 
                           (SELECT 
                                    precio.id_producto as code,
                                    precio.nombre as articulo,
                                    medida.id_medida as idMedida,
                                    medida.nombre as medida,
                                    CONVERT(DATE,getdate()) as fecha_actual,
                                    avg(vaciado.precioProducto) as Precio_actual
                            FROM diaco_vaciadocba vaciado          
                            INNER JOIN diaco_productocba precio
                                ON precio.id_producto = vaciado.idProducto 
                            INNER JOIN diaco_medida medida
                                ON medida.id_medida = vaciado.idMedida
                            INNER JOIN diaco_usuario usuario
                                ON usuario.id_usuario = vaciado.idVerificador
                            INNER JOIN diaco_sede sede
                                ON sede.id_diaco_sede = usuario.id_sede_diaco 
                            INNER JOIN diaco_plantillascba plantilla
                                ON plantilla.idProducto = vaciado.idProducto
                            INNER JOIN diaco_categoriacba categorias
                                ON categorias.id_Categoria = plantilla.idCategoria
                            WHERE convert(date,vaciado.created_at) <= '".$date_last ."'
                                AND sede.id_diaco_sede = ".$id."
                                and categorias.id_Categoria = ".$idCatetoria."
                                and vaciado.precioProducto > 0
                            GROUP BY precio.nombre, medida.nombre,precio.id_producto, medida.id_medida)  t1
                    full outer JOIN 
                            (SELECT 
                                    precio.id_producto as code,
                                    precio.nombre as articulo,
                                    medida.id_medida as idMedida,
                                    medida.nombre as medida,
                                    DATEADD(DAY,-1,CONVERT(DATE,getdate())) as fecha_actual,
                                    avg(vaciado.precioProducto) as Precio_actual2
                            FROM diaco_vaciadocba vaciado          
                            INNER JOIN diaco_productocba precio
                                ON precio.id_producto = vaciado.idProducto 
                            INNER JOIN diaco_medida medida
                                ON medida.id_medida = vaciado.idMedida
                            INNER JOIN diaco_usuario usuario
                                ON usuario.id_usuario = vaciado.idVerificador
                            INNER JOIN diaco_sede sede
                                ON sede.id_diaco_sede = usuario.id_sede_diaco 
                            INNER JOIN diaco_plantillascba plantilla
                                ON plantilla.idProducto = vaciado.idProducto
                            INNER JOIN diaco_categoriacba categorias
                                ON categorias.id_Categoria = plantilla.idCategoria
                            WHERE  convert(date,vaciado.created_at) <= '".$date_previous."'
                                    AND sede.id_diaco_sede = ".$id."
                                and categorias.id_Categoria = ".$idCatetoria."
                                and vaciado.precioProducto > 0
                            GROUP BY precio.nombre, medida.nombre,precio.id_producto, medida.id_medida) t2
                    ON t1.code = t2.code
                    where t1.idMedida = t2.idMedida
                
        ");

    return $price;

    }

    public function apiPrice($id,$idCategoria){

        $last = $this->getPriceNivel2($id,$idCategoria);
        $previous = $this->getPricePrevious($id,$idCategoria);
        $n2 = $this->getPriceLast($id,$idCategoria);

        $getDataPrices = $this->getPriceLastPrevious($id,$idCategoria);


        $convert = collect($getDataPrices);
        $array_price = array();
        $array_n2 = array();
        foreach ($last as $nivel1) {
            foreach($getDataPrices as $nivel2){
                    if($nivel1->code  == $nivel2->code){
                        $data = $convert->where('code',$nivel1->code);

                                array_push($array_n2,[
                                    'code' =>$nivel1->code,
                                    'name' => $nivel1->articulo,
                                    'uom' => $data
                                ]);

                    }
            }
        }

        $codigo = $this->array_unique2($array_n2);
        return fractal()
        ->collection($codigo)
        ->transformWith(new PricesData())
        ->includeCharacters()
        ->toArray();


        // return response()->json($fractal, 200);

    }

    // apirest de diaco
    public function getIdDepartamento(){
        $FiltroDepartamentos = DB::select("SELECT distinct sede.id_diaco_sede,sede.codigo_municipio,sede.nombre_sede,muni.nombre_municipio,
        depa.codigo_departamento,depa.nombre_departamento,
        coordenada.latitut, coordenada.longitud
    FROM diaco_sede sede
        INNER JOIN municipio muni
            ON muni.codigo_municipio = sede.codigo_municipio
        INNER JOIN departamento depa
            ON depa.codigo_departamento = muni.codigo_departamento
        INNER JOIN diaco_coordenadas_cba coordenada
		    ON coordenada.id_sede = sede.id_diaco_sede
        INNER JOIN diaco_usuario usuario
            ON usuario.id_sede_diaco = sede.id_diaco_sede
        INNER JOIN diaco_vaciadocba vaciado
            ON vaciado.idVerificador = usuario.id_usuario
        WHERE id_diaco_sede in (
		SELECT distinct idSede FROM diaco_asignarsedecba asig
		INNER JOIN diaco_vaciadocba vv
		ON vv.idPlantilla = asig.idPlantilla)");
        return $FiltroDepartamentos;
    }

    public function getApi()
    {
        $departamentos = Departamento::with('sede')
            ->whereHas('sede')
            ->get();
        $cate = DB::select("SELECT distinct categoria.id_Categoria as code, categoria.nombre as name 
                                    FROM diaco_plantillascba plantilla
                                    INNER JOIN diaco_categoriacba categoria
                                    ON categoria.id_Categoria = plantilla.idCategoria
                                    WHERE plantilla.NombrePlantilla in 
                                    (SELECT distinct NombreTemplate FROM diaco_name_template_cba template
                                    INNER JOIN diaco_vaciadocba vaciado
                                        ON template.id = vaciado.idPlantilla) ");

        $dep = $this->getIdDepartamento();

        $array_departamentos = [];
        $array_sede= [];

        // dd($cate);

        foreach ($departamentos as $departamento) {
            $sedes = $departamento->sede;
            array_push($array_departamentos,[
                "asdf" => $sedes
            ]);

            // foreach ($dep as $key) {
            //     foreach($sedes as $idSede){
            //         if($departamento->codigo_departamento == $key->codigo_departamento)  {
            //             if($idSede->code == $key->id_diaco_sede){

            //                 array_push($array_departamentos,
            //                 [
            //                     [
            //                         "code" => $departamento->codigo_departamento,
            //                         "name" => $departamento->nombre_departamento,
            //                         "branch" => $idSede,
            //                         "category" => $cate
            //                         // "latitude" => $key->latitut,
            //                         // "longitude" => $key->longitud
            //                         ]
            //                 ]);
            //             }
            //         }
            //     }
            // }

        }


        return response()->json($array_departamentos, 200);
        // return response()->json($array_departamentos, 200);
    }
    // *********************************************

    function array_unique2($a)
    {
        $n = array();
        foreach ($a as $k=>$v) if (!in_array($v,$n))$n[$k]=$v;
        return $n;
    }

    public function collectionDepartamento(){

       $departamento = DB::select("SELECT distinct 
                                    depa.codigo_departamento as code,
                                    depa.nombre_departamento as name,
                                    muni.codigo_municipio as code_muni
                                    FROM diaco_vaciadocba vaciado
                                    INNER JOIN diaco_usuario usuario
                                        on usuario.id_usuario = vaciado.idVerificador
                                    INNER JOIN diaco_sede sede
                                        ON sede.id_diaco_sede = usuario.id_sede_diaco
                                    INNER JOIN municipio muni
                                        ON muni.codigo_municipio = sede.codigo_municipio
                                    INNER JOIN departamento depa
                                        ON depa.codigo_departamento = muni.codigo_departamento

                                    ");
        return $departamento;
    }

    public function collectionSede(){

        $sede = DB::select("SELECT distinct 
                                sede.id_diaco_sede as code,
                                sede.nombre_sede as name,
                                sede.codigo_municipio as code_depa,
                                coordenada.latitut,
	                            coordenada.longitud
                                FROM diaco_vaciadocba vaciado
                                INNER JOIN diaco_usuario usuario
                                    on usuario.id_usuario = vaciado.idVerificador
                                INNER JOIN diaco_sede sede
                                    ON sede.id_diaco_sede = usuario.id_sede_diaco
                                INNER JOIN municipio muni
                                    ON muni.codigo_municipio = sede.codigo_municipio
                                INNER JOIN departamento depa
                                    ON depa.codigo_departamento = muni.codigo_departamento
                                INNER JOIN diaco_coordenadas_cba coordenada
		                                ON coordenada.id_sede = sede.id_diaco_sede");
        return $sede;

    }

    public function collectionCategoria(){

        $categoria = DB::select("SELECT distinct 
                                    plantilla.idCategoria,
                                    categoria.nombre,
                                    sede.id_diaco_sede as sede_id,
                                    sede.nombre_sede as name_sede
                                    FROM diaco_vaciadocba vaciado
                                    INNER JOIN diaco_usuario usuario
                                        on usuario.id_usuario = vaciado.idVerificador
                                    INNER JOIN diaco_name_template_cba template
                                        ON template.id = vaciado.idPlantilla
                                    INNER JOIN diaco_plantillascba plantilla
                                        ON plantilla.NombrePlantilla = template.NombreTemplate
                                    INNER JOIN diaco_categoriacba categoria 
                                        ON categoria.id_Categoria = plantilla.idCategoria
                                    INNER JOIN diaco_sede sede
                                        ON sede.id_diaco_sede = usuario.id_sede_diaco");
        return $categoria;

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

        //return response()->json($response, 200);
    }

    public function collectionDataApi(){
        $depa = $this->collectionDepartamento();
        $sede = $this->collectionSede();
        $categoria = $this->collectionCategoria();
        // dd($categoria);
        $convert = collect($sede);
        $ccategoria = collect($categoria);
        $array_data = [];
        $array_sede_categoria = [];
        $array_categorias = [];
        foreach ($depa as $departamento) {
            foreach ($sede as $sedes) {
                $dataSede = $convert->where('code_depa',$departamento->code_muni);
                $array_sede_categoria[] = [
                    'sede' => $dataSede,
                    // 'categoria' => $sedes_data
                ];
            }
        }
        dd($array_sede_categoria);

        $array_sede = $this->array_unique2($array_sede_categoria);
        // $dconvert = collect($array_sede);
        foreach($categoria as $sedes_data){
            foreach ($sede as $data_sede) {
                $d = $ccategoria->where('sede_id',$data_sede->code);
                $array_categorias[] = [
                    'dataS' => $data_sede,
                    'cateS' => $d
                ];
            }
        }

        $infoSede = $this->array_unique2($array_categorias);


        $array_sede = collect($array_sede);


        foreach ($depa as $departamento) {
            foreach ($sede as $sedes) {
                $dataSede = $convert->where('code_depa',$departamento->code_muni);
                // $sedes_data = $dconvert->where('sede_id',$dsede->code);
                foreach ($categoria as $cate) {
                    array_push($array_data,[
                                'code'  => $departamento->code,
                                'name'  => $departamento->name,
                                'sedes' => $infoSede,
                                            // 'categorias' =>$sedes_data

                    ]);
                }
            }
        }
        //
        $codigo = $this->array_unique2($array_data);

        return fractal()
            ->collection($codigo)
            ->transformWith(new DataDepartamento())
            ->includeCharacters()
            ->toArray();



        // return response()->json($codigo, 200);

    }
}
