<?php

namespace App\Http\Traits\General;

trait DynamicFilter
{
    /*

    EJEMPLO DE ARREGLO DE FILTRO
    [
        "name" => [
            [
                "operation" => "equal",
                "value" => "PRUEBA"
            ],
            [
                "operation" => "equal",
                "value" => "PRUEBA"
            ],
        ],

        "name_2" => [
            [
                "operation" => "equal",
                "value" => "PRUEBA"
            ],
            [
                "operation" => "equal",
                "value" => "PRUEBA"
            ],
        ],
    ];

    */
    private $operations = [
        "equals" => "=",
        "contains" => "ILIKE",
        "different" => "<>",
        "greater_than" => ">",
        "greater_equals" => ">=",
        "less_than" => "<",
        "less_equals" => "<=",
    ];

    public function filter_by_array($query, $array_filter){

        if(count($array_filter) > 0){
            $query = $query->orWhere(function ($subquery) use ($array_filter) {
                foreach ($array_filter as $key => $group) {
                    $subquery = $subquery->where(function ($subquery_group) use ($key, $group) {
                        foreach ($group as $clause) {
                            // dd($group);
                            $operation = "=";
                            $value = $clause["value"];
                            if(array_key_exists($clause["operation"], $this->operations)){
                                $operation = $this->operations[$clause["operation"]];
                            }
                            if($operation == "ILIKE"){
                                $value = "%$value%";
                            }
                            $subquery_group = $subquery_group->orWhere($key, $operation, $value);
                        }
                    });
                }
            });
        }

        return $query;
    }
}
