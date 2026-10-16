<?php

namespace App\Http\Traits\Equipment;

use App\Librerias\Libreria;
use App\Models\ProductMaintenance;
use App\Models\Schedule;
use Illuminate\Support\Facades\DB;

trait MaintenanceTrait
{

    public function search_maintenance($date_start,$date_end,$product_name, $serie, $customer, $sort, $direction, $rows)
    { 
        $product_name = Libreria::getParam($product_name);
        $list = ProductMaintenance::search(null,$date_start,$date_end,$product_name, $serie, $customer);

        $list = $list->orderBy($sort, $direction)
            ->select('product_maintenances.*', 'products AS product_name','products.model')
            ->paginate($rows);


        return $list;
    }

    public function search_proximomantenimiento($date_start,$date_end,$product_name,$serie,$sort,$direction,$rows){
    	$list = Schedule::join('product_series','product_series.id','=','schedules.product_serie_id')
    			->join('products','products.id','=','product_series.product_id')
    			->join('maintenances','maintenances.product_id','=','products.id')
    			->where('schedules.product_status','like','Y');
    	if($product_name!=""){
    		$list = $list->where('products.name','like','%'.$product_name.'%');
    	}
    	if($serie!=""){
    		$list = $list->where('product_series.serie','like','%'.$serie.'%');
    	}
    	$list = $list->orderBy($sort, $direction)
            ->select('schedules.*','maintenances.period', 'products AS product_name','products.model',DB::raw('(select done_date from product_maintenances where schedule_id=schedules.id and deleted_at is null order by done_date desc limit 1) as ultimomantenimiento'))
            ->paginate($rows);

    	return $list;	
    }
}
