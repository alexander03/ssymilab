<?php

namespace App\Http\Traits\Purchase;

use App\Librerias\Libreria;
use App\Models\DocumentPayment;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

trait InstallmentTrait
{
    //SUCURSAL: null = LA DEL USUARIO, "0" = TODAS, OTRO = ID DE SUCURSAL
    public function search_installments($date_start, $date_end, $number, $supplier,$module_id, $sort, $direction, $rows, $status = null, $branch_office_id = null)
    {
        $date_start = Libreria::getParam($date_start);
        $date_end = Libreria::getParam($date_end);
        $number = Libreria::getParam($number);
        $supplier = Libreria::getParam($supplier);
        $user = Auth::user();
        if(is_null($branch_office_id) || $branch_office_id === ""){
            $branch_office_id = $user->branch_office_id;
        }elseif($branch_office_id == "0"){
            $branch_office_id = "";
        }
        $list = DocumentPayment::search($date_start, $date_end, $number, $supplier, $status, $branch_office_id, $module_id)
            ->select(
                "document_payments.*",
                DB::raw("(CASE WHEN people.type = '1' THEN CONCAT(people.surname_father,' ',people.surname_mother,' ', people.name) ELSE people.business_name END) AS supplier_name"),
                DB::raw("(CASE WHEN people.type = '1' THEN people.dni ELSE people.ruc END) AS supplier_document"),
            );
        $list = $list->orderBy($sort, $direction);
        $list = $list->paginate($rows);

        return $list;
    }

}
