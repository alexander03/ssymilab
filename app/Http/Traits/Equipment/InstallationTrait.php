<?php

namespace App\Http\Traits\Equipment;

use App\Librerias\Libreria;
use App\Models\Schedule;
use Illuminate\Support\Facades\Auth;

trait InstallationTrait
{
    // use DynamicFilter;

    public function search_installation($date_start,$date_end,$product_name, $product_model, $product_serie, $customer_name, $product_status, $status, $brand_id, $sort, $direction, $rows)
    {
        $product_name = Libreria::getParam($product_name);
        $product_serie = Libreria::getParam($product_serie);
        $customer_name = Libreria::getParam($customer_name);
        $list = Schedule::search($date_start,$date_end,$product_name,$product_serie,$customer_name,$product_status,$status,$brand_id,$product_model);

        $list = $list->orderBy($sort, $direction)
            ->select('schedules.*', 'products.name AS product_name', 'products.id AS product_id')
            ->paginate($rows);


        return $list;
    }

    public function search_autocompleteInstallation($product_name, $product_serie, $product_status, $customer_name,$schedule_id)
    {
        $user = Auth::user();
        $list = Schedule::search(null,null,$product_name,$product_serie,$customer_name,$product_status,null)
                    ->select('schedules.*', 'products.name AS product_name', 'products.id AS product_id');
        if(!is_null($schedule_id)){
            $list = $list->where('schedules.id','=',$schedule_id);
        }
        $list = $list->limit(5)->get();
        return $list->map(function($lot){
            return [
                "id" => $lot->id,
                'product_name' => $lot->product_name.' '.$lot->product_serie->serie,
                "customer_name" => $lot->customer_name,
                "product_serie_id" => $lot->product_serie_id,
            ];
        })->toArray();
    }

    public function search_autocompleteMaintenance($product_name, $product_status, $customer_name,$schedule_id)
    {
        $user = Auth::user();
        $list = Schedule::search(null,null,null,null,$customer_name,$product_status,null)
                    ->select('schedules.*', 'products.name AS product_name', 'products.id AS product_id');
        if(!is_null($schedule_id)){
            $list = $list->where('schedules.id','=',$schedule_id);
        }
        if(!is_null($product_name)){
            $list = $list->where(function($sql) use($product_name){
                $sql->where('products.name','ILIKE','%'.$product_name.'%')
                    ->orWhere('product_series.serie','ILIKE','%'.$product_name.'%');
            });
        }
        $list = $list->limit(5)->get();
        return $list->map(function($lot){
            return [
                "id" => $lot->id,
                'product_name' => $lot->product_name.' '.$lot->product_serie->serie,
                "customer_name" => $lot->customer_name,
                "product_serie_id" => $lot->product_serie_id,
            ];
        })->toArray();
    }
}
