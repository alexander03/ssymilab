<?php

namespace App\Imports;

use App\Models\Document;
use App\Models\DocumentDetail;
use App\Models\Product;
use App\Models\Brand;
use App\Models\Unit;
use App\Models\ProductUnit;
use App\Models\ProductMove;
use App\Models\ProductSerie;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use App\Librerias\Libreria;
use Illuminate\Support\Facades\Auth;
use App\Http\Traits\Store\StoreDocumentTrait;
use App\Models\StoreDocument;


class DocAlmacenImport implements ToCollection
{
    use StoreDocumentTrait;
    /**
    * @param Collection $collection
    */
    public function collection(Collection $rows){
    	date_default_timezone_set("America/Lima");
    	$x=0;
		$user        = Auth::User();
        DB::beginTransaction();
        try {

            $detail = [
                "comment" => "CARGA INVENTARIO POR EXCEL"
            ];
            $data = [
                "type" => "C",
                "document_type_id" => 6,
                'user_id' => Auth::user()->id,
                'branch_office_id' => Auth::user()->branch_office_id,
                "date_time" => date("Y-m-d"),
                "number" => $this->getNextNumber(6),
                'detail' => json_encode($detail),
                'dispatched' => 'N',
                'status' => 'Y',
                'client_id' => 1,
                'reason_id' => 25,
            ];
            $document = StoreDocument::create($data);

            foreach ($rows as $row) {
    	        if($x>0 && trim($row[6])!=""){
                	$i = 5;
                    $stock = Libreria::getParam(trim($row[7]),'0');$i++;
                    $barcode = Libreria::getParam(trim($row[5]),'');$i++;
                    $nombre = Libreria::getParam(trim($row[6]),'');$i++;
                    $serie = Libreria::getParam(trim($row[8]),'');$i++;
                    if(isset($row[17])){
                        $model = Libreria::getParam(trim($row[17]),'');$i++;
                    }else{
                        $model = "";
                    }
                    $marca = Libreria::getParam(trim($row[2]),'');$i++;
                    if(isset($row[14])){
                        $condicion = Libreria::getParam(trim($row[14]),'');$i++;
                    }else{
                        $condicion = "";
                    }
                    if(isset($row[15])){
                        $rs = Libreria::getParam(trim($row[15]),'');$i++;
                    }else{
                        $rs = "";
                    }
                    if(isset($row[18])){
                        $unidad = Libreria::getParam(trim($row[18]),'');$i++;
                    }else{
                        $unidad = "CAJA";
                    }
                    if(isset($row[20])){    
                        $pv = Libreria::getParam(trim($row[20]),'0');$i++;
                    }else{
                        $pv = 0;
                    }
                    if(isset($row[19])){
                        $pc = Libreria::getParam(trim($row[19]),'');$i++;
                    }else{
                        $pc = 0;
                    }
                    if(isset($row[16])){
                        $procedencia = Libreria::getParam(trim($row[16]),'');$i++;
                    }else{
                        $procedencia = "";
                    }
                    if(isset($row[13])){
                        $presentation = Libreria::getParam(trim($row[13]),'');$i++;
                    }else{
                        $presentation = "";
                    }
                    $brand = Brand::where('name','ilike',trim($marca).'%')->first();
                    if(is_null($brand)){
                        $brand = new Brand();
                        $brand->name = trim($marca);
                        $brand->save();
                    }
                    $unit = Unit::where('name','ilike',trim($unidad).'%')->first();
                    if(is_null($unit)){
                        $unit = new Unit();
                        $unit->code = "NIU";
                        $unit->name = trim($unidad);
                        $unit->save();
                    }
                    $producto = Product::where("name","ilike",trim($nombre))->where('model','ilike',trim($model))->where('brand_id','=',$brand->id)->first();$i++;
                    if(is_null($producto)){
                        $producto = Product::create([
                            'code' => '',
                            'barcode' => $barcode,
                            'type' => 'R',
                            'name' => mb_strtoupper($nombre),
                            'abbreviation' => null,
                            'igv' => 10,
                            'affects_stock' => 'Y',
                            'security_stock' => "0.00",
                            'specifications' => '',
                            'status' => "Y",
                            'branch_office_id' => Auth::user()->branch_office_id,
                            'brand_id' => $brand->id,
                            'category_id' => 4,
                            'supplier_id' => 16,
                            'base_unit_id' => $unit->id,
                            'base_currency_id' => 1,
                            'source' => $procedencia,
                            'presentation' => $presentation,
                            'model' => $model,
                            'rs' => $rs,
                            'condition' => $condicion,
                        ]);
                        ProductUnit::create([
                            'formula' => 1,
                            'purchase_price' => $pc,
                            'sell_price' => $pv,
                            'whosale_price' => 0,
                            'branch_office_id' => Auth::user()->branch_office_id,
                            'unit_id' => $unit->id,
                            'product_id' => $producto->id,
                            'base_unit_id' => $producto->base_unit_id,
                            'currency_id' => 1,
                        ]);

                    }
                    if($stock>0){
                        $input_detail = [
                            'document_id' => $document->id,
                            'product_id' => $producto->id,
                            'unit_id' => $producto->base_unit_id,
                            'quantity' => $stock,
                            'dispatched_quantity' => 0,
                            'status' => 'N'
                        ];
                        $guide_detail = DocumentDetail::create($input_detail);

                        $type = "I";

                        $input_product_serie = [
                            "product_id" => $producto->id,
                            "serie" => $serie,
                            "status" => "N",
                            'branch_office_id' => $document->branch_office_id,
                        ];
                        $product_serie = ProductSerie::create($input_product_serie);

                        $input_product_move = [
                            'document_id' => $document->id,
                            'guide_detail_id' => $guide_detail->id,
                            'branch_office_id' => $document->branch_office_id,
                            'product_id' => $producto->id,
                            'product_serie_id' => $product_serie->id,
                            'quantity' => $stock,
                            'type' => $type,
                            'status' => "N",
                        ];
                       
                        ProductMove::create($input_product_move);
                    }
                    
    		    }
    		    $x=$x+1;
            }
            DB::commit();
            /*$Venta->subtotal = $total;
            $Venta->total = $total;
            $Venta->save();*/
        } catch (\Exception $th) {

            DB::rollback();

            echo $th->getMessage();
        }
    }
}
