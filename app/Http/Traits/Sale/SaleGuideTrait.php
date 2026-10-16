<?php

namespace App\Http\Traits\Sale;

use App\Librerias\Libreria;
use App\Models\SaleGuide;
use App\Models\ReferralGuide;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

trait SaleGuideTrait
{
    public function search_sale_guides($date_start, $date_end, $reason_id, $number, $supplier, $sort, $direction, $rows)
    {
        $date_start = Libreria::getParam($date_start);
        $date_end = Libreria::getParam($date_end);
        $number = Libreria::getParam($number);
        $reason_id = Libreria::getParam($reason_id);
        $supplier = Libreria::getParam($supplier);
        $user = Auth::user();
        $list = SaleGuide::search($date_start, $date_end, $reason_id, $number, $supplier, null, $user->branch_office_id)
        ->select(
            "documents.*",
            DB::raw("(CASE WHEN people.type = '1' THEN CONCAT(people.surname_father,' ',people.surname_mother,' ', people.name) ELSE people.business_name END) AS client_name"),
        );
        $list = $list->orderBy($sort, $direction);
        $list = $list->paginate($rows);

        return $list;
    }

    public function getNextNumberGuide() : string
    {
        $user = Auth::user();
        if($user->branch_office_id==2){//PRINCIPAL
            $serie = "GT01-";
        }else{
            $serie = "G002-";
        }
        $document = SaleGuide::where('branch_office_id', $user->branch_office_id)
            ->where('number', 'LIKE', $serie . "%")
            ->orderBy('number', 'DESC')->first();

        $document2 = ReferralGuide::where('branch_office_id', $user->branch_office_id)
            ->where('number', 'LIKE', $serie . "%")
            ->orderBy('number', 'DESC')->first();


        if($document && is_null($document2)){
            $number = substr($document->number, 5, 8);
            $number = (int) $number;
            return $serie . str_pad(($number + 1), 8, '0', STR_PAD_LEFT);
        }elseif($document2 && is_null($document)){
            $number = substr($document2->number, 5, 8);
            $number = (int) $number;
            return $serie . str_pad(($number + 1), 8, '0', STR_PAD_LEFT);
        }elseif(is_null($document) && is_null($document2)){
            if($user->branch_office_id==3){
                return $serie . str_pad((0 + 1), 8, '0', STR_PAD_LEFT);
            }elseif($user->branch_office_id==1){
                return $serie . str_pad((0 + 1), 8, '0', STR_PAD_LEFT);
            }
        }else{
            $number = substr($document->number, 5, 8);
            $number2 = substr($document2->number, 5, 8);
            if( ((int)$number) < ((int)$number2) ){
                $number = $number2;
            }
            $number = (int) $number;
            return $serie . str_pad(($number + 1), 8, '0', STR_PAD_LEFT);
        }
        return $serie . "00000001";
    }

    public function search_guide_autocomplete($number)
    {
        $user = Auth::user();
        $documents = SaleGuide::search(null, null,null, $number, null, null, $user->branch_office_ids)
        //->where('documents.status', 'P')
        ->orderBy('documents.date_time', 'DESC')
        ->select('documents.*')->limit(5);

        $documents = ReferralGuide::search(null, null, null, $number, null, null, $user->branch_office_ids)
                    ->orderBy('documents.date_time','DESC')
                    ->select('documents.*')->limit(5)->union($documents)->get();

        return $documents->map(function($document){
            return [
                "id" => $document->id,
                "number" => $document->number,
            ];
        })->toArray();
    }

}
