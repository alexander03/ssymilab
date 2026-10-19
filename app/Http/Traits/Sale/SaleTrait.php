<?php


namespace App\Http\Traits\Sale;

use App\Librerias\Libreria;
use App\Models\Sale;
use App\Models\Payment;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\DocumentPayment;

trait SaleTrait
{
    public function search_sales($date_start, $date_end, $document_type_id, $number, $supplier, $vendedor, $status, $supervisor, $sort, $direction, $rows, $zone_id = null)
    {
        $date_start = Libreria::getParam($date_start);
        $date_end = Libreria::getParam($date_end);
        $number = Libreria::getParam($number);
        $supplier = Libreria::getParam($supplier);
        $document_type_id = Libreria::getParam($document_type_id);
        $user = Auth::user();
        //if($user->typeuser_id!=1){
            $branch_office_id = $user->branch_office_id;
        //}
        $list = Sale::search($date_start, $date_end, $document_type_id, $number, $supplier, $status, $branch_office_id, $vendedor, $supervisor)
            ->select(
                "documents.*",
                DB::raw("(CASE WHEN people.type = '1' THEN CONCAT(people.surname_father,' ',people.surname_mother,' ', people.name) ELSE people.business_name END) AS supplier_name"),
                DB::raw("(CASE WHEN people.type = '1' THEN people.dni ELSE people.ruc END) AS supplier_document"),
                DB::raw("(CASE WHEN person.type = '1' THEN CONCAT(person.surname_father,' ',person.surname_mother,' ', person.name) ELSE person.business_name END) AS person_name"),
                DB::raw("(CASE WHEN supervisor.type = '1' THEN CONCAT(supervisor.surname_father,' ',supervisor.surname_mother,' ', supervisor.name) ELSE supervisor.business_name END) AS supervisor_name")
            );
        $list = $this->filter_zone($list, $zone_id);
        $list = $list->orderBy($sort, $direction);
        $list = $list->paginate($rows);

        return $list;
    }

    public function search_sale_autocomplete($number)
    {
        $user = Auth::user();
        $documents = Sale::search(null, null, null, $number, null, null, $user->branch_office_id,null)
        ->where('documents.status', '<>', 'A')
        ->where('documents.dispatched', 'N')
        ->orderBy('documents.created_at', 'DESC')
        ->select('documents.*')->limit(5)->get();

        return $documents->map(function($document){
            return [
                "id" => $document->id,
                "number" => $document->number,
            ];
        })->toArray();
    }

    public function getNextNumberSale($document_type_id, $detraccion = false) : string
    {
        $user = Auth::user();

        //FACTURA CON DETRACCION: SERIE SIGUIENTE (FF01->FF02, F002->F003)
        $con_detraccion = $detraccion && $document_type_id == 13;

        if($user->branch_office_id==2){//PRINCIPAL
            $serie = ($con_detraccion ? "F02" : "F01") . "-";
        }else{
            $serie = ($con_detraccion ? "003" : "002") . "-";
        }


        if($document_type_id == 12){
            $serie = "B" . $serie;
        }elseif($document_type_id == 13){
            $serie = "F" . $serie;
        }

        $sale = Sale::where('branch_office_id', $user->branch_office_id)
            ->where('document_type_id', $document_type_id)
            ->where('number', 'LIKE', $serie . "%")
            ->orderBy('number', 'DESC')->first();
        if($sale){
            $number = (int) substr($sale->number,5,8);
            return $serie . str_pad(($number + 1), 8, '0', STR_PAD_LEFT);
        }else{
            if($user->branch_office_id==3){
                if($document_type_id == 12){
                    return $serie . str_pad((0 + 1), 8, '0', STR_PAD_LEFT);
                }elseif($document_type_id == 13){
                    return $serie . str_pad((0 + 1), 8, '0', STR_PAD_LEFT);
                }
            }elseif($user->branch_office_id==1){
                if($document_type_id == 12){
                    return $serie . str_pad((0 + 1), 8, '0', STR_PAD_LEFT);
                }elseif($document_type_id == 13){
                    return $serie . str_pad((0 + 1), 8, '0', STR_PAD_LEFT);
                }
            }
        }

        return $serie . "00000001";
    }

    //ZONA DEL CLIENTE: "" = TODAS, "0" = SIN ZONA, OTRO = ID DE ZONA
    private function filter_zone($list, $zone_id)
    {
        if(is_null($zone_id) || $zone_id === ""){
            return $list;
        }
        if($zone_id == "0"){
            return $list->whereNull('people.zone_id');
        }
        return $list->where('people.zone_id', $zone_id);
    }

    public function search_payments($branch_office_id,$date_start, $date_end, $number, $supplier,$module_id, $sort, $direction, $rows, $status = null, $zone_id = null)
    {
        $date_start = Libreria::getParam($date_start);
        $date_end = Libreria::getParam($date_end);
        $number = Libreria::getParam($number);
        $supplier = Libreria::getParam($supplier);
        $user = Auth::user();
        $list = DocumentPayment::search($date_start, $date_end, $number, $supplier, $status, $branch_office_id, $module_id)
            ->leftJoin('zones', 'zones.id', '=', 'people.zone_id')
            ->leftJoin(DB::raw('people as seller'), 'seller.id', '=', 'people.supervisor_id')
            ->select(
                "document_payments.*",
                DB::raw("(CASE WHEN people.type = '1' THEN CONCAT(people.surname_father,' ',people.surname_mother,' ', people.name) ELSE people.business_name END) AS supplier_name"),
                DB::raw("(CASE WHEN people.type = '1' THEN people.dni ELSE people.ruc END) AS supplier_document"),
                "people.id AS client_group_id",
                "people.zone_id AS client_zone_id",
                DB::raw("COALESCE(zones.name, 'SIN ZONA') AS zone_name"),
                DB::raw("(CASE WHEN seller.type = '1' THEN CONCAT(seller.surname_father,' ',seller.surname_mother,' ', seller.name) ELSE seller.business_name END) AS seller_name"),
            );
        $list = $this->filter_zone($list, $zone_id);
        //AGRUPADO POR ZONA ("SIN ZONA" AL FINAL) Y DENTRO POR CLIENTE,
        //CLIENTES ORDENADOS POR SU VENDEDOR ASIGNADO (SIN VENDEDOR AL FINAL) Y LUEGO POR NOMBRE
        $list = $list->orderByRaw('zones.name IS NULL, zones.name, zones.id, seller.id IS NULL, seller_name, supplier_name, people.id');
        $list = $list->orderBy($sort, $direction);
        $list = $list->paginate($rows);

        return $list;
    }

    public function anularPagoTrait($sale_id,$documentpayment_id,$payment_id)
    {
        try {
            $payment = Payment::findOrFail($payment_id);
            $payment->delete();

            //Actualizar saldo del item
            $item = DocumentPayment::findOrFail($documentpayment_id);
            if($payment->type == "E"){
                $item->cash_amount = $item->cash_amount - $payment->amount;
            }elseif($payment->type == "T"){
                $item->card_amount = $item->card_amount - $payment->amount;
            }elseif($payment->type == "D"){
                $item->transfer_amount = $item->transfer_amount - $payment->amount;
            }
            if($item->total_amount<=($item->cash_amount+$item->card_amount+$item->transfer_amount)){
                $status = "Y";
            }else{
                $status = "P";
            }
            $item->status = $status;
            $item->save();

            $sale = Sale::find($sale_id);
            $document_payments = $sale->document_payments;
            $status = "C";$pending=0;$paid=0;
            foreach ($document_payments as $key => $value) {
                if($value->status=="P"){
                    $status = "P";
                }
                $paid = $value->cash_amount + $value->card_amount + $value->transfer_amount + $paid;
            }
            $pending = $sale->total - $paid;
            if($pending==0){
                $status="C";
            }else{
                $status="P";
            }
            $sale->update(["status" => $status, "paid" => $paid, "pending_amount" => $pending]);

            return response()->json(['success' => true, 'message' => 'Pago anulado correctamente']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error al anular el pago']);
        }
    }

}
