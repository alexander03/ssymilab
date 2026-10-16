<?php

namespace App\Http\Traits\Sale;

use App\Librerias\Libreria;
use App\Models\Order;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

trait OrderTrait
{
    public function search_orders($date_start, $date_end, $number, $supplier, $sort, $direction, $rows)
    {
        $date_start = Libreria::getParam($date_start);
        $date_end = Libreria::getParam($date_end);
        $number = Libreria::getParam($number);
        $supplier = Libreria::getParam($supplier);
        $user = Auth::user();
        $list = Order::search($date_start, $date_end, $number, $supplier, null, $user->branch_office_id)
        ->select(
            "documents.*",
            DB::raw("(CASE WHEN people.type = '1' THEN CONCAT(people.surname_father,' ',people.surname_mother,' ', people.name) ELSE people.business_name END) AS client_name"),
            DB::raw("(CASE WHEN people.type = '1' THEN people.dni ELSE people.ruc END) AS client_document"),
            DB::raw("(CASE WHEN people_user.type = '1' THEN CONCAT(people_user.surname_father,' ',people_user.surname_mother,' ', people_user.name) ELSE people_user.business_name END) AS user_name"),
        );
        $list = $list->orderBy($sort, $direction);
        $list = $list->paginate($rows);
        return $list;
    }

    public function getNextNumberOrder() : string
    {
        $user = Auth::user();
        $order = Order::where('branch_office_id', $user->branch_office_id)->orderBy('created_at', 'DESC')->first();

        if($order){
            $number = (int) $order->number;
            return str_pad(($number + 1), 8, '0', STR_PAD_LEFT);
        }
        return "00000001";
    }

    public function search_order_autocomplete($number)
    {
        $user = Auth::user();
        $documents = Order::search(null, null, $number, null, null, $user->branch_office_ids)
        ->where('documents.status', 'P')
        ->orderBy('documents.date_time', 'DESC')
        ->select('documents.*')->limit(5)->get();

        return $documents->map(function($document){
            return [
                "id" => $document->id,
                "number" => $document->number,
            ];
        })->toArray();
    }

}
