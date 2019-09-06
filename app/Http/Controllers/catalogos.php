<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\categoria;
use App\Models\product;
use App\Models\subcategory;
use App\Models\measure;
use App\Models\market;
use App\Models\local;
use App\Models\smarket;
use Illuminate\Support\Facades\DB;

class catalogos extends Controller
{
    public function dataCategory(){
        $categoria = categoria::select('id_Categoria','nombre')->where('status','=','A')->get();
        return $categoria;
    }

    public function deleteByIdCategory(Request $request){
        $deleteById = categoria::where('id_Categoria', $request->id)->update(['status' => 'I']);
        return $deleteById;
    }
    public function addCategory(Request $request){   
        $data = DB::statement("exec addCategoria '" .$request->names. "'");
        if($data){
            return response()->json($data, 200);
        };
    }

    public function UpdateById(Request $request){
        $updateById = categoria::where('id_Categoria', $request->id)->update(['nombre' => $request->name]);
        return response()->json($updateById, 200);
    }

    public function findAllProduct(){
        $product = product::select('id_producto as code','nombre as name')->where('status','=','A')->get();
        return response()->json($product, 200);
    }

    public function addProducto(Request $request){
        $data = DB::statement("exec AddProductoCba '" .$request->names. "'");
        if($data){
            return response()->json($data, 200);
        };
    }

    public function deleteByIdProduct(Request $request){
        $deleteById = product::where('id_producto', $request->id)->update(['status' => 'I']);
        return response()->json($deleteById, 200);
    }

    public function updateByIdProduct(Request $request){
        $updateById = product::where('id_producto', $request->id)->update(['nombre' => $request->name]);
        return response()->json($updateById, 200);
    }

    public function findAllSubCategory(){
        $product = subcategory::select('id_sCategoria as code','nombre as name')->where('status','=','A')->get();
        return response()->json($product, 200);
    }

    public function addSubCategory(Request $request){
        $data = DB::statement("exec addSubCategoria '" .$request->names. "'");
        if($data){
            return response()->json($data, 200);
        };
    }
    public function deleteByIdSubCategory(Request $request){
        $deleteById = subcategory::where('id_sCategoria', $request->id)->update(['status' => 'I']);
        return response()->json($deleteById, 200);
    }

    public function updateByIdSubCategory(Request $request){
        $updateById = subcategory::where('id_sCategoria', $request->id)->update(['nombre' => $request->name]);
        return response()->json($updateById, 200);
    }

    public function findAllmeasure(){
        $product = measure::select('id_medida as code','nombre as name')->where('status','=','A')->get();
        return response()->json($product, 200);
    }

    public function addmeasure(Request $request){
        $data = DB::statement("exec addMedidaCBA '" .$request->names. "'");
        if($data){
            return response()->json($data, 200);
        };
    }
    public function deleteByIdmeasure(Request $request){
        $deleteById = measure::where('id_medida', $request->id)->update(['status' => 'I']);
        return response()->json($deleteById, 200);
    }

    public function updateByIdmeasure(Request $request){
        $updateById = measure::where('id_medida', $request->id)->update(['nombre' => $request->name]);
        return response()->json($updateById, 200);
    }
    public function findAllMarket(){
        $product = market::select('idMercado as code','nombreMercado as name','direccionMercado as address')->where('status','=','A')->get();
        return response()->json($product, 200);
    }
    public function addMarket(Request $request){
        $data = new market;
        $data->nombreMercado = $request->names;
        $data->direccionMercado = $request->address;
        $data->status = $request->status;
        if($data->save()){
            return response()->json($data, 200);
        };
    }
    public function deleteByIdMarket(Request $request){
        $deleteById = market::where('idMercado', $request->id)->update(['status' => 'I']);
        return response()->json($deleteById, 200);
    }
    public function updateByIdMarket(Request $request){
        $updateById = market::where('idMercado', $request->id)->update(['nombreMercado' => $request->name, 'direccionMercado' => $request->address]);
        return response()->json($updateById, 200);
    }

    public function findAllLocal(){
        $product = local::select('idEstablecimiento as code','nombreEstablecimiento as name','direccionEstablecimiento as address')->where('status','=','A')->get();
        return response()->json($product, 200);
    }

    public function addLocal(Request $request){
        $data = new local;
        $data->nombreEstablecimiento = $request->names;
        $data->direccionEstablecimiento = $request->address;
        $data->status = $request->status;
        if($data->save()){
            return response()->json($data, 200);
        };
    }
    public function deleteByIdLocal(Request $request){
        $deleteById = local::where('idEstablecimiento', $request->id)->update(['status' => 'I']);
        return response()->json($deleteById, 200);
    }

    public function updateByIdLocal(Request $request){
        $updateById = local::where('idEstablecimiento', $request->id)->update(['nombreEstablecimiento' => $request->name, 'direccionEstablecimiento' => $request->address]);
        return response()->json($updateById, 200);
    }
    public function findAllSmarket(){
        $product = smarket::select('code','name','address')->where('status','=','A')->get();
        return response()->json($product, 200);
    }

    public function addSmarket(Request $request){
        $data = new smarket;
        $data->name = $request->names;
        $data->address = $request->address;
        $data->status = $request->status;
        if($data->save()){
            return response()->json($data, 200);
        };
    }
    public function deleteByIdSmarket(Request $request){
        $deleteById = smarket::where('code', $request->id)->update(['status' => 'I']);
        return response()->json($deleteById, 200);
    }

    public function updateByIdSmarket(Request $request){
        $updateById = smarket::where('code', $request->id)->update(['name' => $request->name, 'address' => $request->address]);
        return response()->json($updateById, 200);
    }
}