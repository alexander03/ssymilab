<?php

namespace App\Http\Traits\Report;

use App\Librerias\Libreria;
use Illuminate\Support\Facades\DB;

//REPORTE DE PRODUCTOS POR CLIENTE: FACTURAS (13) Y BOLETAS (12) NO ANULADAS
trait ProductClientTrait
{
    private function product_client_query($branch_office_id, $date_start, $date_end, $client, $brand_id, $product, $zone_id)
    {
        $date_start = Libreria::getParam($date_start);
        $date_end = Libreria::getParam($date_end);
        $client = Libreria::getParam($client);
        $brand_id = Libreria::getParam($brand_id);
        $product = Libreria::getParam($product);

        $query = DB::table('documents')
            ->join('document_details', 'document_details.document_id', '=', 'documents.id')
            ->join('people', 'people.id', '=', 'documents.client_id')
            ->join('products', 'products.id', '=', 'document_details.product_id')
            ->join('units', 'units.id', '=', 'document_details.unit_id')
            ->leftJoin('brands', 'brands.id', '=', 'products.brand_id')
            ->whereIn('documents.document_type_id', [12, 13])
            ->where('documents.status', '<>', 'A')
            ->whereNull('documents.deleted_at')
            ->whereNull('document_details.deleted_at');

        if(!is_null($date_start)){
            $query->where('documents.date_time', '>=', $date_start." 00:00:00");
        }
        if(!is_null($date_end)){
            $query->where('documents.date_time', '<=', $date_end." 23:59:59");
        }
        if(!is_null($branch_office_id) && $branch_office_id != ""){
            $query->where('documents.branch_office_id', $branch_office_id);
        }
        if(!is_null($brand_id)){
            $query->where('products.brand_id', $brand_id);
        }
        if(!is_null($product)){
            $query->where(function ($subquery) use ($product) {
                $subquery->where('products.name', 'ILIKE', '%' . $product . '%')
                    ->orWhere('products.code', 'ILIKE', '%' . $product . '%');
            });
        }
        //ZONA DEL CLIENTE: "" = TODAS, "0" = SIN ZONA, OTRO = ID DE ZONA
        if(!is_null($zone_id) && $zone_id !== ""){
            if($zone_id == "0"){
                $query->whereNull('people.zone_id');
            }else{
                $query->where('people.zone_id', $zone_id);
            }
        }
        if(!is_null($client)){
            $query->where(function ($subquery) use ($client) {
                $subquery->where('people.business_name', 'ILIKE', '%' . $client . '%')
                    ->orWhere('people.dni', 'ILIKE', '%' . $client . '%')
                    ->orWhere('people.ruc', 'ILIKE', '%' . $client . '%')
                    ->orWhere(DB::raw("CONCAT(people.surname_father, ' ', people.surname_mother, ' ', people.name)"), 'ILIKE', '%' . $client . '%')
                    ->orWhere(DB::raw("CONCAT(people.name, ' ', people.surname_father, ' ', people.surname_mother)"), 'ILIKE', '%' . $client . '%');
            });
        }

        return $query;
    }

    private function client_name_sql()
    {
        return "(CASE WHEN people.type = '1' THEN CONCAT(people.surname_father,' ',people.surname_mother,' ', people.name) ELSE people.business_name END)";
    }

    //CLIENTES CON SU TOTAL (PAGINADO EN PANTALLA; $rows = null DEVUELVE TODOS)
    public function search_product_clients($branch_office_id, $date_start, $date_end, $client, $brand_id, $product, $zone_id, $rows = null)
    {
        $list = $this->product_client_query($branch_office_id, $date_start, $date_end, $client, $brand_id, $product, $zone_id)
            ->select(
                'people.id as client_id',
                DB::raw($this->client_name_sql()." AS client_name"),
                DB::raw("(CASE WHEN people.type = '1' THEN people.dni ELSE people.ruc END) AS client_document"),
                DB::raw('SUM(document_details.quantity) AS quantity'),
                DB::raw('SUM(document_details.subtotal) AS amount')
            )
            ->groupBy('people.id', 'people.type', 'people.surname_father', 'people.surname_mother', 'people.name', 'people.business_name', 'people.dni', 'people.ruc')
            ->orderBy('client_name', 'asc')
            ->orderBy('people.id', 'asc');

        return is_null($rows) ? $list->get() : $list->paginate($rows);
    }

    //PRODUCTOS (POR UNIDAD) COMPRADOS POR CADA CLIENTE, AGRUPADOS POR CLIENTE
    public function search_product_client_details($client_ids, $branch_office_id, $date_start, $date_end, $client, $brand_id, $product, $zone_id)
    {
        $rows = $this->product_client_query($branch_office_id, $date_start, $date_end, $client, $brand_id, $product, $zone_id)
            ->whereIn('people.id', $client_ids)
            ->select(
                'people.id as client_id',
                'products.id as product_id',
                'products.code as product_code',
                DB::raw("TRIM(CONCAT(products.name, ' ', COALESCE(products.model, ''))) AS product_name"),
                DB::raw("COALESCE(brands.name, 'SIN MARCA') AS brand_name"),
                'units.name as unit_name',
                DB::raw('SUM(document_details.quantity) AS quantity'),
                DB::raw('SUM(document_details.subtotal) AS amount')
            )
            ->groupBy('people.id', 'products.id', 'products.code', 'products.name', 'products.model', 'brands.name', 'units.id', 'units.name')
            ->orderBy('product_name', 'asc')
            ->orderBy('units.name', 'asc')
            ->get();

        //POR CLIENTE: PRODUCTOS Y RESUMEN POR MARCA (SEPARADO POR UNIDAD)
        $result = [];
        foreach ($rows as $row) {
            if(!isset($result[$row->client_id])){
                $result[$row->client_id] = ["products" => [], "brands" => []];
            }
            $result[$row->client_id]["products"][] = $row;
            $brand_key = $row->brand_name."|".$row->unit_name;
            if(!isset($result[$row->client_id]["brands"][$brand_key])){
                $result[$row->client_id]["brands"][$brand_key] = (object) [
                    "brand_name" => $row->brand_name,
                    "unit_name" => $row->unit_name,
                    "quantity" => 0,
                    "amount" => 0,
                ];
            }
            $result[$row->client_id]["brands"][$brand_key]->quantity += $row->quantity;
            $result[$row->client_id]["brands"][$brand_key]->amount += $row->amount;
        }
        foreach ($result as $client_id => $data) {
            ksort($result[$client_id]["brands"]);
        }

        return $result;
    }

    //TOTAL GENERAL POR MARCA Y UNIDAD (TODOS LOS CLIENTES DEL FILTRO)
    public function search_brand_totals($branch_office_id, $date_start, $date_end, $client, $brand_id, $product, $zone_id)
    {
        return $this->product_client_query($branch_office_id, $date_start, $date_end, $client, $brand_id, $product, $zone_id)
            ->select(
                DB::raw("COALESCE(brands.name, 'SIN MARCA') AS brand_name"),
                'units.name as unit_name',
                DB::raw('SUM(document_details.quantity) AS quantity'),
                DB::raw('SUM(document_details.subtotal) AS amount')
            )
            ->groupBy('brands.name', 'units.id', 'units.name')
            ->orderBy('brand_name', 'asc')
            ->orderBy('units.name', 'asc')
            ->get();
    }

    /**
     * REPORTE DE VENTAS POR PRODUCTO: AGRUPADO POR MARCA, PRODUCTOS POR UNIDAD
     */

    //MARCAS CON SU IMPORTE (PAGINADO EN PANTALLA; $rows = null DEVUELVE TODAS)
    public function search_brand_sales($branch_office_id, $date_start, $date_end, $client, $brand_id, $product, $zone_id, $rows = null)
    {
        $list = $this->product_client_query($branch_office_id, $date_start, $date_end, $client, $brand_id, $product, $zone_id)
            ->select(
                'products.brand_id as brand_id',
                DB::raw("COALESCE(brands.name, 'SIN MARCA') AS brand_name"),
                DB::raw('SUM(document_details.subtotal) AS amount')
            )
            ->groupBy('products.brand_id', 'brands.name')
            ->orderBy('brand_name', 'asc')
            ->orderBy('products.brand_id', 'asc');

        return is_null($rows) ? $list->get() : $list->paginate($rows);
    }

    //PRODUCTOS POR UNIDAD DE CADA MARCA Y TOTAL DE LA MARCA POR UNIDAD
    public function search_brand_sale_details($brand_ids, $branch_office_id, $date_start, $date_end, $client, $brand_id, $product, $zone_id)
    {
        $rows = $this->product_client_query($branch_office_id, $date_start, $date_end, $client, $brand_id, $product, $zone_id)
            ->whereIn('products.brand_id', $brand_ids)
            ->select(
                'products.brand_id as brand_id',
                'products.code as product_code',
                DB::raw("TRIM(CONCAT(products.name, ' ', COALESCE(products.model, ''))) AS product_name"),
                'units.name as unit_name',
                DB::raw('SUM(document_details.quantity) AS quantity'),
                DB::raw('SUM(document_details.subtotal) AS amount')
            )
            ->groupBy('products.brand_id', 'products.id', 'products.code', 'products.name', 'products.model', 'units.id', 'units.name')
            ->orderBy('product_name', 'asc')
            ->orderBy('units.name', 'asc')
            ->get();

        $result = [];
        foreach ($rows as $row) {
            if(!isset($result[$row->brand_id])){
                $result[$row->brand_id] = ["products" => [], "units" => []];
            }
            $result[$row->brand_id]["products"][] = $row;
            if(!isset($result[$row->brand_id]["units"][$row->unit_name])){
                $result[$row->brand_id]["units"][$row->unit_name] = (object) ["unit_name" => $row->unit_name, "quantity" => 0, "amount" => 0];
            }
            $result[$row->brand_id]["units"][$row->unit_name]->quantity += $row->quantity;
            $result[$row->brand_id]["units"][$row->unit_name]->amount += $row->amount;
        }
        foreach ($result as $key => $data) {
            ksort($result[$key]["units"]);
        }

        return $result;
    }

    //TOTAL GENERAL POR UNIDAD (TODAS LAS MARCAS DEL FILTRO)
    public function search_sales_total_by_unit($branch_office_id, $date_start, $date_end, $client, $brand_id, $product, $zone_id)
    {
        return $this->product_client_query($branch_office_id, $date_start, $date_end, $client, $brand_id, $product, $zone_id)
            ->select(
                'units.name as unit_name',
                DB::raw('SUM(document_details.quantity) AS quantity'),
                DB::raw('SUM(document_details.subtotal) AS amount')
            )
            ->groupBy('units.id', 'units.name')
            ->orderBy('units.name', 'asc')
            ->get();
    }
}
