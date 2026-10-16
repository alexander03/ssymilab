<?php

namespace App\Http\Traits\Sale;

use App\Librerias\Libreria;
use App\Models\Checkout;
use App\Models\CheckoutDetail;
use App\Models\CheckoutDocument;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

trait SaleCheckoutTrait
{
    public function search_sale_checkouts($checkout_id, $date_start, $date_end, $supplier, $sort, $direction, $rows)
    {
        $date_start = Libreria::getParam($date_start);
        $date_end = Libreria::getParam($date_end);
        $supplier = Libreria::getParam($supplier);
        $checkout_id = Libreria::getParam($checkout_id);
        $user = Auth::user();

        $checkout_detail = Checkout::last_datail($checkout_id);
        $open_document_id = 0;
        $close_document_id = null;
        if($checkout_detail){
            $open_document_id = $checkout_detail->open_document_id;
            $close_document_id = $checkout_detail->close_document_id;
        }

        $list = CheckoutDocument::search($checkout_id, $open_document_id, $close_document_id, $date_start, $date_end, $supplier, null, $user->branch_office_id)
        ->select(
            "documents.*",
            DB::raw("(CASE WHEN people.type = '1' THEN CONCAT(people.surname_father,' ',people.surname_mother,' ', people.name) ELSE people.business_name END) AS client_name"),
            DB::raw("(CASE WHEN people.type = '1' THEN people.dni ELSE people.ruc END) AS client_document"),
        );
        $list = $list->orderBy($sort, $direction);
        if($rows > 0){
            $list = $list->paginate($rows);
        }else{
            $list = $list->get();
        }
        return $list;
    }

    public function report_sale_checkouts($checkout_id, $checkout_detail_id,$date_start, $date_end, $supplier, $sort, $direction, $rows)
    {
        $date_start = Libreria::getParam($date_start);
        $date_end = Libreria::getParam($date_end);
        $supplier = Libreria::getParam($supplier);
        $checkout_id = Libreria::getParam($checkout_id);
        $user = Auth::user();

        $checkout_detail = CheckoutDetail::find($checkout_detail_id);
        $open_document_id = 0;
        $close_document_id = null;
        if($checkout_detail){
            $open_document_id = $checkout_detail->open_document_id;
            $close_document_id = $checkout_detail->close_document_id;
        }

        $list = CheckoutDocument::search($checkout_id, $open_document_id, $close_document_id, $date_start, $date_end, $supplier, null, $user->branch_office_id)
        ->select(
            "documents.*",
            DB::raw("(CASE WHEN people.type = '1' THEN CONCAT(people.surname_father,' ',people.surname_mother,' ', people.name) ELSE people.business_name END) AS client_name"),
            DB::raw("(CASE WHEN people.type = '1' THEN people.dni ELSE people.ruc END) AS client_document"),
        );
        $list = $list->orderBy($sort, $direction);
        if($rows > 0){
            $list = $list->paginate($rows);
        }else{
            $list = $list->get();
        }
        return $list;
    }

    public function getNextNumber($document_type_id) : string
    {
        $user = Auth::user();
        $proforma = CheckoutDocument::where('branch_office_id', $user->branch_office_id)->where('document_type_id', $document_type_id)->orderBy('id', 'DESC')->first();

        if($proforma){
            $number = (int) $proforma->number;
            // debug($number);
            return str_pad(($number + 1), 8, '0', STR_PAD_LEFT);
        }
        return "00000001";
    }

    public function clear_resume(){
        foreach ($this->resume as $key => $value) {
            $this->resume[$key]["total_apertura"] = 0.00;
            $this->resume[$key]["total_apertura_efectivo"] = 0.00;
            $this->resume[$key]["total_apertura_tarjeta"] = 0.00;
            $this->resume[$key]["total_apertura_deposito"] = 0.00;
            $this->resume[$key]["total_ventas"] = 0.00;
            $this->resume[$key]["total_ventas_efectivo"] = 0.00;
            $this->resume[$key]["total_ventas_tarjeta"] = 0.00;
            $this->resume[$key]["total_ventas_deposito"] = 0.00;
            $this->resume[$key]["total_varios"] = 0.00;
            $this->resume[$key]["total_varios_efectivo"] = 0.00;
            $this->resume[$key]["total_varios_tarjeta"] = 0.00;
            $this->resume[$key]["total_varios_deposito"] = 0.00;
            $this->resume[$key]["total_egresos"] = 0.00;
            $this->resume[$key]["total_egresos_efectivo"] = 0.00;
            $this->resume[$key]["total_egresos_tarjeta"] = 0.00;
            $this->resume[$key]["total_egresos_deposito"] = 0.00;
            $this->resume[$key]["total_saldo"] = 0.00;
            $this->resume[$key]["total_saldo_efectivo"] = 0.00;
            $this->resume[$key]["total_saldo_tarjeta"] = 0.00;
            $this->resume[$key]["total_saldo_deposito"] = 0.00;
        }
    }

    public function calculate_resume($checkout_id, $sort, $direction){
        $list_total = $this->search_sale_checkouts($checkout_id, null, null, null, $sort, $direction, 0);

        $this->clear_resume();

        foreach ($list_total as $value) {
            foreach ($this->resume as $key => $item) {
                if($item["currency_id"] == $value->currency_id){
                    if($value->document_type_id == 10){
                        if($value->reason->name == "INGRESO INICIAL"){
                            $this->resume[$key]["total_apertura"] = $this->resume[$key]["total_apertura"] + $value->total;
                            $this->resume[$key]["total_apertura_efectivo"] = $this->resume[$key]["total_apertura_efectivo"] + $value->cash_amount;
                            $this->resume[$key]["total_apertura_tarjeta"] = $this->resume[$key]["total_apertura_tarjeta"] + $value->card_amount;
                            $this->resume[$key]["total_apertura_deposito"] = $this->resume[$key]["total_apertura_deposito"] + $value->transfer_amount;
                        }elseif($value->reason->name == "PAGO DE VENTA"){
                            $this->resume[$key]["total_venta"] = $this->resume[$key]["total_venta"] + $value->total;
                            $this->resume[$key]["total_venta_efectivo"] = $this->resume[$key]["total_venta_efectivo"] + $value->cash_amount;
                            $this->resume[$key]["total_venta_tarjeta"] = $this->resume[$key]["total_venta_tarjeta"] + $value->card_amount;
                            $this->resume[$key]["total_venta_deposito"] = $this->resume[$key]["total_venta_deposito"] + $value->transfer_amount;
                        }else{
                            $this->resume[$key]["total_varios"] = $this->resume[$key]["total_varios"] + $value->total;
                            $this->resume[$key]["total_varios_efectivo"] = $this->resume[$key]["total_varios_efectivo"] + $value->cash_amount;
                            $this->resume[$key]["total_varios_tarjeta"] = $this->resume[$key]["total_varios_tarjeta"] + $value->card_amount;
                            $this->resume[$key]["total_varios_deposito"] = $this->resume[$key]["total_varios_deposito"] + $value->transfer_amount;
                        }
                        $this->resume[$key]["total_saldo"] = $this->resume[$key]["total_saldo"] + $value->total;
                        $this->resume[$key]["total_saldo_efectivo"] = $this->resume[$key]["total_saldo_efectivo"] + $value->cash_amount;
                        $this->resume[$key]["total_saldo_tarjeta"] = $this->resume[$key]["total_saldo_tarjeta"] + $value->card_amount;
                        $this->resume[$key]["total_saldo_deposito"] = $this->resume[$key]["total_saldo_deposito"] + $value->transfer_amount;
                    }else{
                        $this->resume[$key]["total_egresos"] = $this->resume[$key]["total_egresos"] + $value->total;
                        $this->resume[$key]["total_egresos_efectivo"] = $this->resume[$key]["total_egresos_efectivo"] + $value->cash_amount;
                        $this->resume[$key]["total_egresos_tarjeta"] = $this->resume[$key]["total_egresos_tarjeta"] + $value->card_amount;
                        $this->resume[$key]["total_egresos_deposito"] = $this->resume[$key]["total_egresos_deposito"] + $value->transfer_amount;

                        $this->resume[$key]["total_saldo"] = $this->resume[$key]["total_saldo"] - $value->total;
                        $this->resume[$key]["total_saldo_efectivo"] = $this->resume[$key]["total_saldo_efectivo"] - $value->cash_amount;
                        $this->resume[$key]["total_saldo_tarjeta"] = $this->resume[$key]["total_saldo_tarjeta"] - $value->card_amount;
                        $this->resume[$key]["total_saldo_deposito"] = $this->resume[$key]["total_saldo_deposito"] - $value->transfer_amount;
                    }

                }
            }
        }
    }

}
