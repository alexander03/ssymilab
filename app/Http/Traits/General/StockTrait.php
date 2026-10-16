<?php

namespace App\Http\Traits\General;

use App\Librerias\Libreria;
use App\Models\Currency;
use App\Models\Kardex;
use App\Models\ProductMove;
use App\Models\ProductUnit;
use App\Models\Stock;
use Illuminate\Support\Facades\Auth;

trait StockTrait
{

    public function search_stock($product_id, $product_name,$type, $brand_id, $sort, $direction, $rows)
    {
        $product_id = Libreria::getParam($product_id);
        $product_name = Libreria::getParam($product_name);
        $user = Auth::user();
        $list = Stock::search($product_id, $product_name, $user->branch_office_id,$type)
            ->select('stocks.*', 'products.name as product_name','products.code','products.model','products.presentation');
        if (!is_null($brand_id) && $brand_id!="") {
            $list = $list->where('products.brand_id', '=', $brand_id);
        }
        $list = $list->orderBy($sort, $direction);
        $list = $list->paginate($rows);

        return $list;
    }

    public function search_stockreport($product_id, $product_name, $branch_office_id, $brand_id, $type, $sort, $direction, $rows)
    {
        $product_id = Libreria::getParam($product_id);
        $product_name = Libreria::getParam($product_name);
        $user = Auth::user();
        $list = Stock::search($product_id, $product_name, $branch_office_id,$type)
            ->select('stocks.*', 'products.name as product_name','products.model','products.presentation','products.code');
        if (!is_null($brand_id) && $brand_id!="") {
            $list = $list->where('products.brand_id', '=', $brand_id);
        }
        $list = $list->orderBy($sort, $direction);
        $list = $list->paginate($rows);

        return $list;
    }

    public function increase_stock($document, $detail){

        $stock = Stock::verificar_existencia($detail->product_id, $document->branch_office_id);

        $product = $detail->product;

        $product_unit = ProductUnit::where('product_id', $product->id)->where('unit_id', $detail->unit_id)->orderBy('id', 'asc')->first();//->where('branch_office_id', $document->branch_office_id)

        if(!$product_unit){
            throw new \Exception("No existe unidad [{$detail->unit->name}] para [{$product->name}] [{$product->model}] - ID: [{$product->id}]");
        }

        $quantity = $detail->dispatched_quantity * $product_unit->formula;

        if($stock){

            $stock->update([
                'real' => $stock->real + $quantity,
                'available' => $stock->available + $quantity
            ]);


        }else{
            $input_stock = [
                'real' => $quantity,
                'separated' => 0,
                'available' => $quantity,
                'product_id' => $detail->product_id,
                'unit_id' => $product->base_unit_id,
                'branch_office_id' => $document->branch_office_id,
            ];
            Stock::create($input_stock);

        }

        $product_moves = ProductMove::where('guide_detail_id', $detail->id)->get();//->where('document_id',$document->id)

        if(count($product_moves) == 0){
            throw new \Exception("No se encontraron series ni lotes para [{$product->name}]");
        }

        foreach ($product_moves as $key => $product_move) {
            if($product->type == "M" || $product->type == "E" || $product->type == "C"){
                $product_serie = $product_move->product_serie;
                if($product_move->status == "Y"){
                    $product_serie->update([
                        'status' => 'Y'
                    ]);
                }
            }else{
                $lot = $product_move->lot;
                if($lot->status == "Y"){
                    // $lot->quantity = $lot->quantity + $product_move->quantity;
                    $lot->update([
                        'remain' => $lot->remain + $product_move->dispatched_quantity
                    ]);
                }else{
                    $lot->update([
                        'remain' => $product_move->dispatched_quantity,
                        'status' => 'Y'
                    ]);
                }

            }
        }


        $price = 0;
        $document_id = $document->id;
        $currency_id = Currency::base()->id;
        $exchange_rate = 1;

        if($document->document_id && $document->status!="A"){
            if(!is_null($detail->document_detail)){
                $price = $detail->document_detail->purchase_price;
            }
            $document_id = $document->document_id;
            $currency_id = $document->document->currency_id;
            $exchange_rate = $document->document->exchange_rate;
        }

        $input_kardex = [
            'type' => 'I',
            'quantity' => $quantity,
            'price' => $price,
            'product_id' => $detail->product_id,
            'unit_id' => $detail->unit_id,
            'branch_office_id' => $document->branch_office_id,
            'document_id' => $document_id,
            'currency_id' => $currency_id,
            'exchange_rate' => $exchange_rate,
        ];
        Kardex::create($input_kardex);
    }

    public function increase_stock2($document, $detail){

        $stock = Stock::verificar_existencia($detail->product_id, $document->branch_office_id);

        $product = $detail->product;

        $product_unit = ProductUnit::where('product_id', $product->id)->where('unit_id', $detail->unit_id)->orderBy('id', 'asc')->first();//->where('branch_office_id', $document->branch_office_id)

        if(!$product_unit){
            throw new \Exception("No existe unidad [{$detail->unit->name}] para [{$product->name}] [{$product->model}] - ID: [{$product->id}]");
        }

        $quantity = $detail->dispatched_quantity * $product_unit->formula;

        if($stock){

            $stock->update([
                'real' => $stock->real + $quantity,
                'available' => $stock->available + $quantity
            ]);


        }else{
            $input_stock = [
                'real' => $quantity,
                'separated' => 0,
                'available' => $quantity,
                'product_id' => $detail->product_id,
                'unit_id' => $product->base_unit_id,
                'branch_office_id' => $document->branch_office_id,
            ];
            Stock::create($input_stock);

        }

        $product_moves = ProductMove::where('document_detail_id', $detail->id)->get();//->where('document_id',$document->id)

        if(count($product_moves) == 0){
            throw new \Exception("No se encontraron series ni lotes para [{$product->name}]");
        }

        foreach ($product_moves as $key => $product_move) {
            if($product->type == "M" || $product->type == "E" || $product->type == "C"){
                $product_serie = $product_move->product_serie;
                if($product_move->status == "Y"){
                    $product_serie->update([
                        'status' => 'Y'
                    ]);
                }
            }else{
                $lot = $product_move->lot;
                if($lot->status == "Y"){
                    // $lot->quantity = $lot->quantity + $product_move->quantity;
                    $lot->update([
                        'remain' => $lot->remain + $product_move->dispatched_quantity
                    ]);
                }else{
                    $lot->update([
                        'remain' => $product_move->dispatched_quantity,
                        'status' => 'Y'
                    ]);
                }

            }
        }


        $price = 0;
        $document_id = $document->id;
        $currency_id = Currency::base()->id;
        $exchange_rate = 1;

        $price = $detail->sell_price;        

        $input_kardex = [
            'type' => 'I',
            'quantity' => $quantity,
            'price' => $price,
            'product_id' => $detail->product_id,
            'unit_id' => $detail->unit_id,
            'branch_office_id' => $document->branch_office_id,
            'document_id' => $document_id,
            'currency_id' => $currency_id,
            'exchange_rate' => $exchange_rate,
        ];
        Kardex::create($input_kardex);
    }

    public function decrease_stock($document, $detail){

        $product = $detail->product;

        $stock = Stock::verificar_existencia($detail->product_id, $document->branch_office_id);

        if(!$stock){
            throw new \Exception("No existen unidades de [{$product->name}] registradas");
        }

        $product_unit = ProductUnit::where('product_id', $product->id)->where('unit_id', $detail->unit_id)->orderBy('id', 'asc')->first();//->where('branch_office_id', $document->branch_office_id)

        if(!$product_unit){
            throw new \Exception("No existe unidad [{$detail->unit->name}] para [{$product->name}]");
        }

        $quantity = $detail->dispatched_quantity * $product_unit->formula;

        if($stock->available - $quantity < 0){
            throw new \Exception("No hay cantidad de unidades para retirar (ID".$product->name.")");
        }

        $stock->update([
            'real' => $stock->real - $quantity,
            'available' => $stock->available - $quantity
        ]);

        $product_moves = ProductMove::where('guide_detail_id', $detail->id)->where('document_id',$document->id)->get();

        if(count($product_moves) == 0){
            throw new \Exception("No se encontraron series ni lotes para [{$product->name}]");
        }

        foreach ($product_moves as $key => $product_move) {
            if($product->type == "M" || $product->type == "E" || $product->type == "C"){
                if($product_move->status == "Y"){
                    $status = "V";
                    if(in_array($document->document_type_id, [7,8])){
                        $status = "S";
                    }
                    $product_serie = $product_move->product_serie;
                    $product_serie->update([
                        'status' => $status
                    ]);
                }
            }else{
                $lot = $product_move->lot;
                if(($lot->remain - $product_move->dispatched_quantity) < 0){
                    throw new \Exception("Cantidad insuficiente en el lote [{$lot->lot}] para retirar ");
                }
                $lot->update([
                    'remain' => $lot->remain - $product_move->dispatched_quantity
                ]);
            }
        }

        $product_move->update([
            'status' => 'Y',
        ]);

        $price = 0;
        $document_id = $document->id;
        $currency_id = Currency::base()->id;
        $exchange_rate = 1;

        if($document->document_id && !is_null($detail->document_detail)){//AQUI SALDRA LA GUIA Y NO LA VENTA, YA QUE NO ESTA REFERENCIADO EL DETALLE DE LA GUIA AL DETALLE DE LA VENTA
            $price = $detail->document_detail->purchase_price;
            $document_id = $document->document_id;
            $currency_id = $document->document->currency_id;
            $exchange_rate = $document->document->exchange_rate;
        }

        $input_kardex = [
            'type' => 'E',
            'quantity' => $quantity,
            'price' => $price,
            'product_id' => $detail->product_id,
            'unit_id' => $detail->unit_id,
            'branch_office_id' => $document->branch_office_id,
            'document_id' => $document_id,
            'currency_id' => $currency_id,
            'exchange_rate' => $exchange_rate,
        ];
        Kardex::create($input_kardex);
    }

    public function decrease_available($document, $detail){

        $product = $detail->product;

        $stock = Stock::verificar_existencia($detail->product_id, $document->branch_office_id);

        if(!$stock){
            throw new \Exception("No existen unidades de [{$product->name}] registradas");
        }

        $product_unit = ProductUnit::where('product_id', $product->id)->where('unit_id', $detail->unit_id)->orderBy('id', 'asc')->first();//->where('branch_office_id', $document->branch_office_id)

        if(!$product_unit){
            throw new \Exception("No existe unidad [{$detail->unit->name}] para [{$product->name}]");
        }

        $quantity = $detail->quantity * $product_unit->formula;

        if($stock->available - $quantity < 0){
            throw new \Exception("No hay cantidad de unidades para retirar para [{$product->name}]");
        }

        $stock->update([
            'available' => $stock->available - $quantity
        ]);

    }

}
