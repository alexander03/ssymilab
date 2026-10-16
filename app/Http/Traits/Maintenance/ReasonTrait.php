<?php

namespace App\Http\Traits\Maintenance;

use App\Librerias\Libreria;
use App\Models\Reason;

trait ReasonTrait
{
    // use DynamicFilter;

    public function search_reasons($name,$document_type, $sort, $direction, $rows)
    {
        $name = Libreria::getParam($name);
        $list = Reason::search($name,null,null);
        if(count($document_type)>0){
        	$list = $list->whereIn('document_type_id',$document_type);
        }
        $list = $list->orderBy($sort, $direction)
            ->paginate($rows);


        return $list;
    }
}
