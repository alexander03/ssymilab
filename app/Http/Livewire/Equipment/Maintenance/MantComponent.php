<?php

namespace App\Http\Livewire\Equipment\Maintenance;

use Livewire\Component;
use App\Models\Job;
use App\Models\Schedule;
use App\Models\ProductMaintenance;
use App\Models\ProductMoveMaintenance;
use App\Models\ProductMaintenanceDetail;
use App\Models\Product;
use App\Models\Person;
use App\Models\Lot;
use App\Models\ProductMove;
use App\Models\ProductSerie;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use App\Http\Traits\Maintenance\PersonTrait;
use App\Http\Traits\Equipment\InstallationTrait;
use App\Http\Traits\Maintenance\ProductTrait;
use App\Http\Traits\Store\LotTrait;
use App\Http\Traits\Store\SerieTrait;
use Illuminate\Support\Facades\Auth;

class MantComponent extends Component
{
	use PersonTrait;
	use SerieTrait;
	use InstallationTrait;
    use ProductTrait;
    use LotTrait;

	public $modal;
    public $event_modal;

    public $product;
    public $schedule;
    public $productmaintenance;
    public $customer;

    public $product_name;
    public $installed_date;
    public $scheduled_date;
    public $done_date;
    public $product_location;
    public $product_place;
    public $customer_name;
    public $product_serie_id;
    public $status,$list_status;
    public $type;
    public $head_name;
    public $staff_name;
    public $observations;
    public $types;
    public $branch_office;

    public $search_list_people;
    public $search_list_product;

    public $search_product;
    public $barcode;
    public $list_search_product;
    public $shopping_car;
    public $document_detail_key;

    public $search_list_lot;
    public $search_list_serie;
    public $search_serie;

    public $modal_document_detail = false;


    public function mount(ProductMaintenance $productmaintenance, bool $modal, string $event_modal)
    {
        $this->productmaintenance = $productmaintenance;
        $this->modal = $modal;
        $this->event_modal = $event_modal;
        $this->search_list_people = [];
        $this->search_list_product = [];
        $this->search_list_lot = [];
        $this->search_list_serie = [];
        $this->product_serie_id = 0;
        $this->schedule = null;
        $this->type="P";
        $this->shopping_car = [];
        $this->search_product = "";
        $branch_office = Auth::user()->branch_office;

        if($productmaintenance->id != null){
        	$this->schedule = $productmaintenance->schedule;
            $this->done_date = $productmaintenance->done_date;
            $this->scheduled_date = $productmaintenance->scheduled_date;
            $this->customer_name = $productmaintenance->schedule->customer_name;
            $this->observations = $productmaintenance->observations;
            $this->head_name = $productmaintenance->head_name;
            $this->staff_name = $productmaintenance->staff_name;
            $this->status = $productmaintenance->status;
            $this->product_name = $productmaintenance->schedule->product_serie->product->name." - ".$productmaintenance->schedule->product_serie->serie;
            $customer = $productmaintenance->schedule->customer;
            $this->type = $productmaintenance->type;

            $details = ProductMaintenanceDetail::where('product_maintenance_id','=',$productmaintenance->id)->get();
            foreach ($details as $key => $value) {
                $product = Product::find($value["product_id"]);
                if($product){
                    $list = ProductMoveMaintenance::where('product_maintenance_detail_id','=',$value->id)->get();
                    $moves = array();
                    foreach ($list as $key => $value) {
                        if(is_null($value->lot)){
                            $data = array("product_serie_id" => $value->product_serie_id,
                                "product_serie" => $value->product_serie->serie,
                                "lot_id" => null,
                                "lot_barcode" => '',
                                "lot_name" => '',
                                "lot_due_date" => '',
                                "quantity" => $value->quantity,
                                "id" => $value->id);
                        }else{
                            $data = array("product_serie_id" => "",
                                "product_serie" => "",
                                "lot_id" => $value->lot->id,
                                "lot_barcode" => $value->lot->barcode,
                                "lot_name" => $value->lot->lot,
                                "lot_due_date" => $value->lot->due_date,
                                "quantity" => $value->quantity,
                                "id" => $value->id);
                        }
                        $moves[] =$data;
                    }
                    $this->shopping_car[] = [
                        "product_id" => $value["product_id"],
                        "product_name" => $product->name,
                        "product_name_original" => $product->name,
                        "product_type" => $product->type,
                        "model" => $product->model,
                        "unit_id" => $value["unit_id"],
                        "currency_id" => $value["currency_id"],
                        "units" => $product->all_only_units,
                        "quantity" => sprintf('%g', $value["quantity"]),
                        "guia" => $value["guia"],
                        "product_moves" => $moves,
                    ];
                }
            }
        }else{
            $this->installed_date = "";
            $this->customer_name = "";
            $this->product_place = "";
            $this->product_location = "";
            $this->done_date = date("Y-m-d");
            $customer = null;
        }

        $this->customer = [
            'id' => ($customer) ? $customer->id : "",
            'type' => ($customer) ? $customer->type : "6",
            'name_complete' => ($customer) ? $customer->getNameComplete() : "",
            'document_number' => ($customer) ? $customer->getDocumentNumber() : "",
        ];

        $this->branch_office = [
            'id' => $branch_office->id,
            'name' => $branch_office->name,
            'company_name' => $branch_office->company->business_name,
            'company_ruc' => $branch_office->company->ruc,
            'address' => $branch_office->address,
            'department_name' => $branch_office->department->name,
            'province_name' => $branch_office->province->name,
            'district_name' => $branch_office->district->name,
        ];

        $this->customer_name = $this->customer["name_complete"];
        $this->list_search_product = [];
        $this->types = ["P" => "Preventivo", "C" => "Correctivo", "A"=>"Asistencia", "I"=>"Instalacion"];
        $this->search_products();

    }

    public function render()
    {
        return view('livewire.equipment.maintenance.mant-component');
    }


    public function back_button(){
        if (!$this->modal){
            redirect()->route('maintenance');
        }else{
            // $this->emit($this->event_modal);
        }
    }

    /**
     * Funciones para filtro de Personas
     */
    public function updatingCustomerName($value)
    {
        if($value != ""){
            $this->search_list_people = $this->search_autocomplete($value, "C");
        }else{
            $this->search_list_people = [];
        }
    }

    public function select_active_person($person_id){
        $person = Person::find($person_id);
        if($person){
            $this->customer = [
                "id" => $person->id,
                'type' => $person->type,
                "name_complete" => $person->getNameComplete(),
                "document_number" => $person->getDocumentNumber()
            ];
            $this->customer_name = $this->customer["name_complete"];
        }
    }

    public function updatingType(){
        $this->search_products();
    }

    /**
     * Funciones para filtro de Producto
     */
    public function updatingProductName($value)
    {
        if($value != ""){
            $this->search_list_product = $this->search_autocompleteMaintenance($value, 'Y', null,null);
        }else{
            $this->search_list_product = [];
        }
    }

    public function select_active_product($schedule_id){
        $product = $this->search_autocompleteInstallation(null, null, 'Y', null,$schedule_id);
        if($product){
        	$this->product_serie_id = $product[0]["product_serie_id"];
            $this->product_name = $product[0]["product_name"];
            $this->customer_name = $product[0]["customer_name"];
            $this->schedule = Schedule::find($product[0]["id"]);
        }
    }

    /**
     * Funciones para buscar productos
     */
    public function search_products(){
        $supplier_id = null;
        if($this->type=="C"){
            $type="E";
        }elseif($this->type=="A"){
            $type="R";
        }elseif($this->type=="I"){
            $type="C";
        }else{
            $type="";
        }
        $this->list_search_product = $this->search_products_fast(null, $this->search_product, $supplier_id,$type);
    }

    public function select_products($key){
        if(array_key_exists($key, $this->list_search_product)){
            $focus_quantity_id = "quantity_".$this->list_search_product[$key]["id"];
            $this->emit('focus', [
                'id' => $focus_quantity_id
            ]);
            foreach ($this->shopping_car as $key2 => $value) {
                if($value["product_id"] == $this->list_search_product[$key]["id"]){
                    $this->shopping_car[$key2] = [
                        "product_id" => $this->list_search_product[$key]["id"],
                        "product_name" => $this->list_search_product[$key]["name"],
                        "product_type" => $this->list_search_product[$key]["type"],
                        "unit_id" => $this->list_search_product[$key]["base_unit_id"],
                        "units" => $this->list_search_product[$key]["units"],
                        "quantity" => $this->shopping_car[$key2]["quantity"] + 1,
                        "model" => $this->list_search_product[$key]["model"],
                        "guia" => $this->list_search_product[$key]["guia"],
                        "product_moves" => $this->shopping_car[$key2]["product_moves"],
                    ];
                    return true;
                }
            }
            $this->shopping_car[] = [
                "product_id" => $this->list_search_product[$key]["id"],
                "product_name" => $this->list_search_product[$key]["name"],
                "unit_id" => $this->list_search_product[$key]["base_unit_id"],
                "units" => $this->list_search_product[$key]["units"],
                "product_type" => $this->list_search_product[$key]["type"],
                "quantity" => 1,
                "guia" => $this->list_search_product[$key]["guia"],
                "model" => $this->list_search_product[$key]["model"],
                "product_moves" => [],
            ];
        }
    }

    public function quit_car_item($key){
        unset($this->shopping_car[$key]);
        $this->product_serie = "";
        $this->product_due_date = "";
        $this->lot_quantity = "";
        $this->product_lot = "";
        $this->product_barcode = "";
        $this->search_lot = "";
        $this->document_detail_key = null;
    }

    public function select_bar_code(){
        if(trim($this->barcode) != ""){
            $searching = $this->search_products_fast($this->barcode, null);
            if(count($searching) == 0){
                $this->emit('alert-warning', [
                    'title' => 'Error!',
                    'text' => 'No se encontró producto.',
                ]);
                return true;
            }
            $product_search = $searching[0];
            $focus_quantity_id = "quantity_".$product_search["id"];
            $this->emit('focus', [
                'id' => $focus_quantity_id
            ]);
            foreach ($this->shopping_car as $key2 => $value) {
                if($value["product_id"] == $product_search["id"]){
                    $this->shopping_car[$key2] = [
                        "product_id" => $product_search["id"],
                        "product_name" => $product_search["name"],
                        "unit_id" => $product_search["base_unit_id"],
                        "units" => $product_search["units"],
                        "quantity" => $this->shopping_car[$key2]["quantity"] + 1,
                    ];
                    return true;
                }
            }
            $this->shopping_car[] = [
                "product_id" => $product_search["id"],
                "product_name" => $product_search["name"],
                "unit_id" => $product_search["base_unit_id"],
                "units" => $product_search["units"],
                "quantity" => 1,
                "guia" => "",
            ];
        }
    }

    /**
     * Funciones para filtro de Lote
     */
    public function updatingSearchLot($value)
    {
        if($value != ""){
            $this->search_list_lot = $this->search_autocompleteLot($value, $this->shopping_car[$this->document_detail_key]["product_id"]);
        }else{
            $this->search_list_lot = [];
        }
    }

    public function select_active_lot($lot_id){
        $lot = Lot::find($lot_id);
        if($lot){
            $this->product_due_date = $lot->due_date;
            $this->lot_quantity = $lot->lot_quantity;
            $this->product_lot = $lot->lot;
            $this->product_barcode = $lot->barcode;
            $this->lot_remain = sprintf('%g', $lot->remain);
        }
    }

    /**
     * FUNCIONES PARA ASIGNAR SERIES O LOTES
     */
    public function add_product_move($key){
        $this->modal_document_detail = true;
        $this->document_detail_key = "" . $key;
        $this->clear_product_moves_data();
    }

    public function updatingSearchSerie($value)
    {
        //if($value != ""){
            $product_serie = ProductSerie::where('serie','like', '%'.$this->search_serie.'%')
                ->where('product_id', $this->shopping_car[$this->document_detail_key]["product_id"])
                ->where('branch_office_id', $this->branch_office["id"])
                ->where('status', '<>', 'A')->get();
            $list = $product_serie->map(function($serie){
                        return [
                            "id" => $serie->id,
                            'serie' => $serie->serie,
                        ];
                    })->toArray();

            $this->search_list_serie = $list;
        /*}else{
            $this->search_list_serie = [];
        }*/
    }

    public function select_active_serie($serie_id){
        $serie = ProductSerie::find($serie_id);
        if($serie){
            $this->search_serie = $serie->serie;
            $this->add_product_serie($serie->id);
        }
    }

    public function add_product_serie($serie_id){
        /*if(!$this->product_serie ||trim($this->product_serie) == "" ){
            return true;
        }*/
        $product_serie = ProductSerie::find($serie_id);
        $this->product_serie = $product_serie->serie;

        if(array_key_exists($this->document_detail_key, $this->shopping_car)){

            foreach ($this->shopping_car[$this->document_detail_key]["product_moves"] as $key => $value) {
                if($this->product_serie == $value["product_serie"]){
                    $this->emit('alert-warning', [
                        'title' => 'Cuidado!',
                        'text' => 'Ya agregó esa serie.',
                    ]);
                    return true;
                }
            }

            /*$product_serie = ProductSerie::where('serie', $this->product_serie)
                ->where('product_id', $this->shopping_car[$this->document_detail_key]["product_id"])
                ->where('branch_office_id', $this->branch_office["id"])
                ->where('status', '<>', 'A')->first();*/

            if(false){
                if($product_serie && $product_serie->status != "S" && $product_serie->status != "V"){
                    $this->emit('alert-warning', [
                        'title' => 'Cuidado!',
                        'text' => 'La serie se encuentra activa.',
                    ]);
                    return true;
                }
                $this->shopping_car[$this->document_detail_key]["product_moves"][] = [
                    "id" => "",
                    "product_id" => $this->shopping_car[$this->document_detail_key]["product_id"],
                    "product_name" => $this->shopping_car[$this->document_detail_key]["product_name"],
                    "product_type" => $this->shopping_car[$this->document_detail_key]["product_type"],
                    "product_serie_id" => $product_serie ? $product_serie->id : "",
                    "product_serie" => $this->product_serie,
                    "lot_id" => "",
                    "lot_name" => "",
                    "lot_due_date" => "",
                    "quantity" => "1",
                ];
            }else{
                if(!$product_serie || $product_serie->status != "Y"){
                    $this->emit('alert-warning', [
                        'title' => 'Cuidado!',
                        'text' => 'No se encontró la serie.',
                    ]);
                    return true;
                }
                $this->shopping_car[$this->document_detail_key]["product_moves"][] = [
                    "id" => "",
                    "product_id" => $this->shopping_car[$this->document_detail_key]["product_id"],
                    "product_name" => $this->shopping_car[$this->document_detail_key]["product_name"],
                    "product_type" => $this->shopping_car[$this->document_detail_key]["product_type"],
                    "product_serie_id" => $product_serie->id,
                    "product_serie" => $this->product_serie,
                    "lot_id" => "",
                    "lot_name" => "",
                    "lot_due_date" => "",
                    "quantity" => "1",
                ];
            }


            //$this->update_dispatched_quantity();
            $this->product_serie = "";
            $this->product_due_date = "";
            $this->lot_quantity = "";
            $this->product_lot = "";
            $this->product_barcode = "";
            $this->search_lot = "";
        }
    }

    public function add_product_lot(){

        if(!$this->product_lot ||trim($this->product_lot) == "" || !$this->product_due_date ||trim($this->product_due_date) == "" || !$this->lot_quantity ||trim($this->lot_quantity) == "" ||trim($this->lot_quantity) == "0"){
            return true;
        }

        if(array_key_exists($this->document_detail_key, $this->shopping_car)){

            foreach ($this->shopping_car[$this->document_detail_key]["product_moves"] as $key => $value) {
                if($this->product_lot == $value["lot_name"]){
                    $this->emit('alert-warning', [
                        'title' => 'Cuidado!',
                        'text' => 'Ya agregó ese lote.',
                    ]);
                    return true;
                }
            }

            $lot = Lot::where('lot', $this->product_lot)
                ->where('product_id', $this->shopping_car[$this->document_detail_key]["product_id"])
                ->where('branch_office_id', $this->branch_office["id"])
                ->where('status', '<>', 'A')->first();

            if(false){
                if($lot && $lot->status != "Y"){
                    $this->emit('alert-warning', [
                        'title' => 'Cuidado!',
                        'text' => 'El lote no se encuentra disponible.',
                    ]);
                    return true;
                }
                $this->shopping_car[$this->document_detail_key]["product_moves"][] = [
                    "id" => "",
                    "product_id" => $this->shopping_car[$this->document_detail_key]["product_id"],
                    "product_name" => $this->shopping_car[$this->document_detail_key]["product_name"],
                    "product_type" => $this->shopping_car[$this->document_detail_key]["product_type"],
                    "product_serie_id" => "",
                    "product_serie" => "",
                    "lot_id" => $lot ? $lot->id : "",
                    "lot_name" => $this->product_lot,
                    "lot_due_date" => $this->product_due_date,
                    "quantity" => $this->lot_quantity,
                ];
            }else{
                if(!$lot || $lot->status != "Y" || $lot->remain - $this->lot_quantity < 0){
                    $this->emit('alert-warning', [
                        'title' => 'Cuidado!',
                        'text' => 'El lote no puede ser disminuido.',
                    ]);
                    return true;
                }
                $this->shopping_car[$this->document_detail_key]["product_moves"][] = [
                    "id" => "",
                    "product_id" => $this->shopping_car[$this->document_detail_key]["product_id"],
                    "product_name" => $this->shopping_car[$this->document_detail_key]["product_name"],
                    "product_type" => $this->shopping_car[$this->document_detail_key]["product_type"],
                    "product_serie_id" => "",
                    "product_serie" => "",
                    "lot_id" => $lot->id,
                    "lot_barcode" => $lot->barcode,
                    "lot_name" => $this->product_lot,
                    "lot_due_date" => $this->product_due_date,
                    "quantity" => $this->lot_quantity,
                ];
            }
            $this->clear_product_moves_data();
        }
    }

    private function clear_product_moves_data(){
        $this->product_serie = "";
        $this->product_due_date = "";
        $this->lot_quantity = "";
        $this->product_lot = "";
        $this->product_barcode = "";
        $this->search_lot = "";
        $this->lot_remain = "";
    }

    public function search_product_lot(){
        $search = $this->search_lot;
        $lot = Lot::where(function($sql) use($search) {
                $sql->where('lot','ilike', '%'.$search.'%')->orWhere('barcode','ilike','%'.$search.'%');
            })
                ->where('product_id', $this->shopping_car[$this->document_detail_key]["product_id"])
                ->where('branch_office_id', $this->branch_office["id"])
                ->where('status', '<>', 'A')->first();

        if($lot){
            $this->product_due_date = $lot->due_date;
            $this->lot_quantity = $lot->lot_quantity;
            $this->product_lot = $lot->lot;
            $this->product_barcode = $lot->barcode;
            $this->lot_remain = sprintf('%g', $lot->remain);
        }
    }

    public function quit_product_move($key){
        unset($this->shopping_car[$this->document_detail_key]["product_moves"][$key]);
        $this->update_dispatched_quantity();
    }

    public function save(){
        $inputs = [
            "schedule_id" => $this->schedule->id,
            "product_serie_id" => $this->schedule->product_serie_id,
            "type" => $this->type,
            "scheduled_date" => $this->scheduled_date,
            "done_date" => $this->done_date,
            "status" => "Y",
            "product_status" => "Y",
            "head_name" => $this->head_name,
            "staff_name" => $this->staff_name,
            "observations" => $this->observations,
        ];

        // Validator::make(
        //     $inputs,
        //     $form_request->rules(),
        //     $form_request->messages(),
        //     $form_request->validationAttributes(),
        // )->validate();

        DB::beginTransaction();
        try {

            if(!$this->productmaintenance->id){
                $productmaintenance = ProductMaintenance::create($inputs);
            }else{
                $productmaintenance = ProductMaintenance::find($this->productmaintenance->id);
                $productmaintenance->update($inputs);
            }

            foreach ($this->shopping_car as $key => $item) {
                $input_detail = [
                    'product_maintenance_id' => $productmaintenance->id,
                    'product_id' => $item["product_id"],
                    'unit_id' => $item["unit_id"],
                    'quantity' => $item["quantity"],
                    'guia' => $item["guia"],
                ];
                $detail = ProductMaintenanceDetail::create($input_detail);

                foreach ($item["product_moves"] as $key => $product_move) {
                    if($product_move["product_type"] == "M" || $product_move["product_type"] == "E" || $product_move["product_type"] == "C"){
                        $product_serie = ProductSerie::find($product_move["product_serie_id"]);
                        $input_product_move = [
                            'product_maintenance_id' => $productmaintenance->id,
                            'product_maintenance_detail_id' => $detail->id,
                            'branch_office_id' => $this->branch_office["id"],
                            'product_id' => $product_move["product_id"],
                            'product_serie_id' => $product_serie->id,
                            'quantity' => $product_move["quantity"],
                        ];
                    }else{
                        $lot = Lot::find($product_move["lot_id"]);
                        $input_product_move = [
                            'product_maintenance_id' => $productmaintenance->id,
                            'product_maintenance_detail_id' => $detail->id,
                            'branch_office_id' => $this->branch_office["id"],
                            'product_id' => $product_move["product_id"],
                            'lot_id' => $lot->id,
                            'quantity' => $product_move["quantity"],
                        ];
                    }
                    if($product_move["id"] != ""){
                        ProductMoveMaintenance::find($product_move["id"])->update($input_product_move);
                    }else{
                        ProductMoveMaintenance::create($input_product_move);
                    }
                }

            }

            DB::commit();

            if (!$this->modal){
                Session::flash('success', 'Se registró correctamente');
                redirect()->route('maintenance');
            }


        } catch (\Exception $th) {

            DB::rollback();

            $this->emit('alert-error', [
                'title' => '¡Error!',
                'text' => 'Algo salio mal: ' . $th->getMessage(),
            ]);
        }
    }
}
