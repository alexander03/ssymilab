<?php

namespace App\Http\Traits\Maintenance;

use App\Librerias\Libreria;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

trait ProductTrait
{

    public function search_products($type, $name, $category_id, $brand_id, $supplier_id, $status, $sort, $direction, $rows, $model='')
    {
        //$branch_office_id = Auth::user()->branch_office_id;
        $branch_office_id = null;
        $type = Libreria::getParam($type);
        $name = Libreria::getParam($name);
        $category_id = Libreria::getParam($category_id);
        $brand_id = Libreria::getParam($brand_id);
        $supplier_id = Libreria::getParam($supplier_id);
        $status = Libreria::getParam($status);

        $list = Product::search($type, $name, $category_id, $brand_id, $supplier_id, $status, $branch_office_id);
        if($model!=""){
            $list = $list->where('products.model','ILIKE','%'.$model.'%');
        }
        $list = $list->select('products.*');
        switch ($sort) {
            case 'supplier':
                $list = $list->orderBy('people.surname_father', $direction)
                    ->orderBy('people.surname_mother', $direction)
                    ->orderBy('people.name', $direction)
                    ->orderBy('people.business_name', $direction);
                break;
            default:
                $list = $list->orderBy($sort, $direction);
                break;
        }
        $list = $list->paginate($rows);


        return $list;
    }

    public function search_products_fast($barcode, $search, $supplier_id = null, $type='',$id=0)
    {
        $user = Auth::user();
        $products = Product::search_fast($barcode, $search, $user->branch_office_id, $supplier_id)
        ->where(function($sql) use($type){
            if($type!=""){
                $sql->where('products.type','like',$type);
            }
        })
        ->where(function($sql) use($id){
            if($id!="0"){
                $sql->where('products.id','=',$id);
            }
        })
        ->orderBy('products.name', 'ASC')
        ->select(
            'products.*',
            DB::raw('stocks.available as available'),
            DB::raw('units.name as base_unit_name'),
            DB::raw('units.code as base_unit_code'),
            DB::raw('units.id as base_unit_id'),
            DB::raw('categories.name as category_name'),
            DB::raw('brands.name as brand_name'),
            DB::raw("(CASE WHEN people.type = '1' THEN CONCAT(people.surname_father,' ',people.surname_mother,' ', people.name) ELSE people.business_name END) AS supplier_name")
        )
        ->get();

        return $products->map(function($product){
            return [
                "id" => $product->id,
                "name" => $product->name,
                "presentation" => $product->presentation,
                "code" => $product->code,
                "type" => $product->type,
                "category_name" => $product->category_name,
                "supplier_name" => $product->supplier_name,
                "brand_name" => $product->brand_name,
                "base_unit_id" => $product->base_unit_id,
                "base_unit_name" => $product->base_unit_name,
                "base_unit_code" => $product->base_unit_code,
                "units_name" => $product->all_only_units->map(function($unit){
                    return $unit->name;
                })->toArray(),
                "units" => $product->all_only_units,
                "delivery_times" => ($product->available>0?'INMEDIATA':''),
                "due_dates" => is_null($product->all_due_dates)?[''=>'']:$product->all_due_dates,
                "stock" => sprintf('%g', $product->available),
                "sell_price" => $product->all_product_units->map(function($unit) use($product){
                    if($product->base_unit_id==$unit->unit_id){
                        return $unit->sell_price;
                    }
                })->toArray(),
                "model" => $product->model,
                "purchase_price"=>$product->base_product_unit()->purchase_price,
                "sell_price_min" => $product->all_product_units->map(function($unit) use($product){
                    if($product->base_unit_id==$unit->unit_id){
                        return $unit->sell_price_min;
                    }
                })->toArray(),
            ];
        })->toArray();
    }

    public function getNextCode() : string
    {
        $user = Auth::user();
        $data = Product::orderBy('code', 'DESC')->orderBy('name','asc')->first();//where('branch_office_id', $user->branch_office_id)
        if($data){
            $number = (int) substr($sale->number,0,8);
            return str_pad(($number + 1), 8, '0', STR_PAD_LEFT);
        }else{
            return str_pad((0 + 1), 8, '0', STR_PAD_LEFT);
        }

        return "00000001";
    }
}
