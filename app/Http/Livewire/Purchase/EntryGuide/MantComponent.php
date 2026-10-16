<?php

namespace App\Http\Livewire\Purchase\EntryGuide;

use App\Http\Traits\Maintenance\PersonTrait;
use App\Http\Traits\Maintenance\ProductTrait;
use App\Http\Traits\Purchase\PurchaseTrait;
use App\Models\DocumentDetail;
use App\Models\DocumentType;
use App\Models\EntryGuide;
use App\Models\Lot;
use App\Models\Person;
use App\Models\Product;
use App\Models\ProductMove;
use App\Models\ProductSerie;
use App\Models\Purchase;
use App\Models\Role;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Livewire\Component;

class MantComponent extends Component
{
    use PersonTrait;
    use ProductTrait;
    use PurchaseTrait;

    protected $aditional_actions;
    protected $arr_actions;

    public $entry_guide;
    public $modal_supplier = false;
    public $modal_document_detail = false;

    public $document_type_id, $client_id, $document_id, $number, $date_time, $comment;

    public $document_type_name;
    public $branch_office;
    public $supplier;
    public $document_types;

    public $search_document;
    public $search_list_document;

    public $search_person;
    public $search_list_people;

    public $list_search_product;
    public $shopping_car;

    public $document_detail_key;
    public $product_serie;
    public $product_lot;
    public $product_barcode;
    public $product_due_date;
    public $lot_quantity;

    protected $listeners = [
        'toggle-modal-supplier' => 'toggle_supplier',
    ];

    public function hydrate()
    {
        $this->resetValidation();
    }

    public function mount(EntryGuide $entry_guide)
    {
        $this->entry_guide = $entry_guide;
        $this->search_list_people = [];
        $this->search_list_document = [];
        $this->list_search_product = [];
        $this->document_types = EntryGuide::list_document_types();

        $this->document_type_id = $this->document_types[0]->id;
        $this->document_type_name = $this->document_types[0]->name;
        $this->date_time = date('Y-m-d');
        $this->comment = "";

        $branch_office = Auth::user()->branch_office;
        $supplier = null;

        $this->shopping_car = [];


        if($entry_guide->id != null){
            $this->document_type_id = $this->entry_guide->document_type_id;
            $this->client_id = $this->entry_guide->client_id;
            $this->document_id = $this->entry_guide->document_id;
            $this->document_type_name = DocumentType::find($this->entry_guide->document_id)->name;
            $this->number = $this->entry_guide->number;
            $this->date_time = date_format(new \DateTime($this->entry_guide->date_time), 'Y-m-d');
            $this->comment = $this->entry_guide->comment();

            $branch_office = $entry_guide->branch_office;
            $supplier = $entry_guide->client;

            $details = $entry_guide->details;
            foreach ($details as $key => $value) {
                $product = Product::find($value["product_id"]);
                if($product){
                    $this->shopping_car[] = [
                        "product_id" => $value["product_id"],
                        "product_name" => $product->name,
                        "unit_id" => $value["unit_id"],
                        "units" => $product->all_only_units,
                        "quantity" => sprintf('%g', $value["quantity"]),
                    ];
                }
            }
        }

        $this->branch_office = [
            'name' => $branch_office->name,
            'company_name' => $branch_office->company->business_name,
            'company_ruc' => $branch_office->company->ruc,
            'address' => $branch_office->address,
            'department_name' => $branch_office->department->name,
            'province_name' => $branch_office->province->name,
            'district_name' => $branch_office->district->name,
        ];
        $this->supplier = [
            'id' => ($supplier) ? $supplier->id : "",
            'type' => ($supplier) ? $supplier->type : "6",
            'name_complete' => ($supplier) ? $supplier->getNameComplete() : "",
            'document_number' => ($supplier) ? $supplier->getDocumentNumber() : "",
        ];
        $this->search_person = $this->supplier["name_complete"];

    }

    public function render()
    {
        $this->load_actions();
        $arr_actions = $this->arr_actions;

        $person = new Person();
        $role_id = Role::where('code', 'P')->first()->id;
        return view('livewire.purchase.entry-guide.mant-component')->with(compact('person', 'role_id', 'arr_actions'));
    }

    /**
     * Función para regresar
     */
    public function back_button(){
        redirect()->route('entry-guide');
    }

    /**
     * Función para cargar permisos de usuario
     */
    protected function load_actions(){
        $this->aditional_actions = Auth::user()->user_type->actions()
            ->whereIn('actions.code', ['create_person'])->get();
        $this->arr_actions = [];
        foreach ($this->aditional_actions as $key => $value) {
            $this->arr_actions[] = $value->code;
        }
    }

    /**
     * Funciones de Modales
     */
    public function toggle_supplier ($supplier_id = null){
        $this->modal_supplier = !$this->modal_supplier;
        if($supplier_id){
            $person = Person::find($supplier_id);
            $this->supplier = [
                "id" => $person->id,
                'type' => $person->type,
                "name_complete" => $person->getNameComplete(),
                "document_number" => $person->getDocumentNumber()
            ];
            $this->search_person = $this->supplier["name_complete"];
        }
    }

    /**
     * Funciones de actualizacion de fields
     */
    public function updatingDocumentTypeId($value)
    {
        $document_type = DocumentType::find($value);
        if($document_type){
            $this->document_type_name = $document_type->name;
        }
    }

    /**
     * Funciones para filtro de Personas
     */
    public function updatingSearchPerson($value)
    {
        if($value != ""){
            $this->search_list_people = $this->search_autocomplete($value, "P");
        }else{
            $this->search_list_people = [];
        }
    }

    public function select_active_person($person_id){
        $person = Person::find($person_id);
        if($person){
            $this->supplier = [
                "id" => $person->id,
                'type' => $person->type,
                "name_complete" => $person->getNameComplete(),
                "document_number" => $person->getDocumentNumber()
            ];
            $this->search_person = $this->supplier["name_complete"];
        }
    }

    /**
     * Funciones para filtro de Compras
     */
    public function updatingSearchDocument($value)
    {
        if($value != ""){
            $this->search_list_document = $this->search_purchase_autocomplete($value);
        }else{
            $this->search_list_document = [];
        }
    }

    public function select_active_document($document_id){
        $document = Purchase::find($document_id);
        if($document){
            $this->document_id = $document->id;
            $this->search_document = $document->number;
            $this->shopping_car = [];
            $this->list_search_product = [];
            $details = $document->details;
            foreach ($details as $key => $detail) {
                $product = Product::find($detail["product_id"]);
                if($product){
                    $this->list_search_product[] = [
                        "id" => $product->id,
                        "document_detail_id" => $detail["id"],
                        "name" => $product->name." ".$product->model." ".$product->presentation,
                        "code" => $product->code,
                        "type" => $product->type,
                        "category_name" => $product->category->name,
                        "supplier_name" => $product->supplier->getNameComplete(),
                        "brand_name" => $product->brand->name,
                        "base_unit_id" => $detail->unit_id,
                        "base_unit_name" => $detail->unit->name,
                        "base_unit_code" => $detail->unit->code,
                        "units_name" => $product->all_only_units->map(function($unit){
                            return $unit->name;
                        })->toArray(),
                        "units" => $product->all_only_units,
                        "quantity" => sprintf('%g', $detail["quantity"]),
                        "dispatched_quantity" => sprintf('%g', $detail["dispatched_quantity"]),
                        "dispatched_quantity_init" => sprintf('%g', $detail["dispatched_quantity"]),
                        "stock" => $product->stock ? sprintf('%g', $product->stock->available) : "0",
                    ];

                    $product_moves = DocumentDetail::product_moves_pending($detail->id);
                    if($detail["dispatched_quantity"] < $detail["quantity"]){
                        $item_car = [
                            "document_detail_id" => $detail["id"],
                            "product_id" => $detail["product_id"],
                            "product_name" => $product->name." ".$product->model." ".$product->presentation,
                            "brand_name" => $product->brand->name,
                            "product_type" => $product->type,
                            "unit_id" => $detail["unit_id"],
                            "unit_name" => $detail->unit->name,
                            "units" => $product->all_only_units,
                            "quantity" => sprintf('%g', $detail["quantity"]),
                            "dispatched_quantity" => sprintf('%g', $detail["dispatched_quantity"]),
                            "dispatched_quantity_init" => sprintf('%g', $detail["dispatched_quantity"]),
                            "product_moves_pending" => [],
                            "product_moves" => [],
                        ];
                        foreach ($product_moves as $key => $product_move) {
                            $item_car["product_moves_pending"][] = [
                                "id" => $product_move->id,
                                "product_id" => $product->id,
                                "product_name" => $product->name." ".$product->model." ".$product->presentation,
                                "brand_name" => $product->brand->name,
                                "product_type" => $product->type,
                                "product_serie_id" => $product_move->product_serie_id,
                                "product_serie" => $product_move->product_serie->serie,
                                "lot_id" => $product_move->lot_id,
                                "lot_name" => $product_move->lot->lot,
                                "lot_due_date" => $product_move->lot->due_date,
                                "quantity" => $product_move->quantity,
                            ];
                        }
                        $this->shopping_car[] = $item_car;
                    }
                }
            }
            if($document->client){
                $this->supplier = [
                    'id' => ($document->client) ? $document->client->id : "",
                    'type' => ($document->client) ? $document->client->type : "6",
                    'name_complete' => ($document->client) ? $document->client->getNameComplete() : "",
                    'document_number' => ($document->client) ? $document->client->getDocumentNumber() : "",
                ];
                $this->search_person = $this->supplier["name_complete"];
            }
        }
    }

    public function quit_document(){
        $this->document_id = null;
        $this->search_document = "";
        $this->shopping_car = [];
        $this->list_search_product = [];
        $this->supplier = [
            'id' => "",
            'type' => "6",
            'name_complete' => "",
            'document_number' => "",
        ];
        $this->search_person = $this->supplier["name_complete"];
        $this->document_detail_key = null;
        $this->product_serie = null;
        $this->product_lot = null;
        $this->product_barcode = null;
        $this->product_due_date = null;
    }

    /**
     * Funciones para buscar productos
     */
    public function select_products($key){
        if(array_key_exists($key, $this->list_search_product)){
            foreach ($this->shopping_car as $key2 => $value) {
                if($value["product_id"] == $this->list_search_product[$key]["id"]){
                    return true;
                }
            }
            if($this->list_search_product[$key]["dispatched_quantity_init"] != $this->list_search_product[$key]["quantity"]){
                $this->shopping_car[] = [
                    "document_detail_id" => $this->list_search_product[$key]["document_detail_id"],
                    "product_id" => $this->list_search_product[$key]["id"],
                    "product_name" => $this->list_search_product[$key]["name"]." ".$this->list_search_product[$key]["model"]." ".$this->list_search_product[$key]["presentation"],
                    "product_type" => $this->list_search_product[$key]["type"],
                    "brand_name" => $this->list_search_product[$key]["brand_name"],
                    "unit_id" => $this->list_search_product[$key]["base_unit_id"],
                    "unit_name" => $this->list_search_product[$key]["base_unit_name"],
                    "units" => $this->list_search_product[$key]["units"],
                    "quantity" => $this->list_search_product[$key]["quantity"],
                    "dispatched_quantity" => $this->list_search_product[$key]["dispatched_quantity"],
                    "dispatched_quantity_init" => $this->list_search_product[$key]["dispatched_quantity"],
                    "product_moves_pending" => [],
                    "product_moves" => [],
                ];
            }
        }
    }

    public function quit_car_item($key){
        unset($this->shopping_car[$key]);
    }

    /**
     * FUNCIONES PARA ASIGNAR SERIES O LOTES
     */
    public function add_product_move($key){
        $this->modal_document_detail = true;
        $this->document_detail_key = "" . $key;
    }

    public function add_product_serie(){
        if($this->shopping_car[$this->document_detail_key]["dispatched_quantity"] == $this->shopping_car[$this->document_detail_key]["quantity"]){
            $this->emit('alert-warning', [
                'title' => 'Cuidado!',
                'text' => 'Ya se agregó la cantidad máxima de series.',
            ]);
            return true;
        }
        if(!$this->product_serie ||trim($this->product_serie) == "" ){
            return true;
        }
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

            $this->shopping_car[$this->document_detail_key]["product_moves"][] = [
                "id" => "",
                "product_id" => $this->shopping_car[$this->document_detail_key]["product_id"],
                "product_name" => $this->shopping_car[$this->document_detail_key]["product_name"],
                "product_type" => $this->shopping_car[$this->document_detail_key]["product_type"],
                "product_serie_id" => "",
                "product_serie" => $this->product_serie,
                "lot_id" => "",
                "lot_name" => "",
                "lot_barcode" => "",
                "lot_due_date" => "",
                "quantity" => "1",
            ];
            $this->update_dispatched_quantity();
            $this->product_serie = "";
            $this->product_due_date = "";
            $this->lot_quantity = "";
            $this->product_lot = "";
            $this->product_barcode = "";
        }
    }

    public function add_product_lot(){
        if($this->shopping_car[$this->document_detail_key]["dispatched_quantity"] == $this->shopping_car[$this->document_detail_key]["quantity"]){
            $this->emit('alert-warning', [
                'title' => 'Cuidado!',
                'text' => 'Ya se agregó la cantidad máxima de lotes.',
            ]);
            return true;
        }
        if(!$this->product_lot ||trim($this->product_lot) == "" || !$this->product_due_date ||trim($this->product_due_date) == "" || !$this->lot_quantity ||trim($this->lot_quantity) == "" ||trim($this->lot_quantity) == "0"){
            return true;
        }
        if($this->shopping_car[$this->document_detail_key]["quantity"] - $this->shopping_car[$this->document_detail_key]["dispatched_quantity"] < $this->lot_quantity){
            $this->emit('alert-warning', [
                'title' => 'Cuidado!',
                'text' => 'La cantidad dada supera el máximo.',
            ]);
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

            $this->shopping_car[$this->document_detail_key]["product_moves"][] = [
                "id" => "",
                "product_id" => $this->shopping_car[$this->document_detail_key]["product_id"],
                "product_name" => $this->shopping_car[$this->document_detail_key]["product_name"],
                "product_type" => $this->shopping_car[$this->document_detail_key]["product_type"],
                "product_serie_id" => "",
                "product_serie" => "",
                "lot_id" => "",
                "lot_name" => $this->product_lot,
                "lot_barcode" => $this->product_barcode,
                "lot_due_date" => $this->product_due_date,
                "quantity" => $this->lot_quantity,
            ];
            $this->update_dispatched_quantity();
            $this->product_serie = "";
            $this->product_due_date = "";
            $this->lot_quantity = "";
            $this->product_barcode = "";
            $this->product_lot = "";
        }
    }

    public function quit_product_move($key){
        unset($this->shopping_car[$this->document_detail_key]["product_moves"][$key]);
        $this->update_dispatched_quantity();
    }

    private function update_dispatched_quantity(){
        foreach ($this->shopping_car as $key => $value) {
            $quantity = $value["dispatched_quantity_init"];
            foreach ($this->shopping_car[$key]["product_moves"] as $product_move) {
                $quantity = $quantity + $product_move["quantity"];
            }
            $this->shopping_car[$key]["dispatched_quantity"] = $quantity;
        }
    }

    /**
     * Funcion para guardar
     */
    private function validate_product_moves(){
        $total = 0;

        foreach ($this->shopping_car as $key => $value) {
            foreach ($this->shopping_car[$key]["product_moves"] as $product_move) {
                $total = $total + $product_move["quantity"];
            }
        }

        if($total == 0){
            $this->emit('alert-error', [
                'title' => 'Error!',
                'text' => 'No ha registrado ninguna serie o lote.',
            ]);
            return false;
        }
        return true;
    }

    public function save(){
        // Validator::make(
        //     $this->inputs_validation(),
        //     $form_request->rules(),
        //     $form_request->messages(),
        //     $form_request->validationAttributes(),
        // )->validate();

        if(!$this->validate_product_moves()){
            return true;
        }
        DB::beginTransaction();
        try {

            $document = EntryGuide::create($this->input_create_entry_guide());

            $purchase = Purchase::find($this->document_id);

            foreach ($this->shopping_car as $key => $item) {
                if(count($item["product_moves"]) > 0){
                    $document_detail = DocumentDetail::find($item["document_detail_id"]);
                    $document_detail->update([
                        'dispatched_quantity' => $item["dispatched_quantity"],
                        'status' => ($document_detail->quantity - $item["dispatched_quantity"] == 0) ? "Y" : "N",
                    ]);
                    $input_detail = [
                        'document_id' => $document->id,
                        'document_detail_id' => $item["document_detail_id"],
                        'product_id' => $item["product_id"],
                        'unit_id' => $item["unit_id"],
                        'quantity' => $item["dispatched_quantity"] - $item["dispatched_quantity_init"],
                        'dispatched_quantity' => 0,
                        'status' => 'N'
                    ];
                    $guide_detail = DocumentDetail::create($input_detail);

                    foreach ($item["product_moves"] as $key => $product_move) {
                        if($product_move["product_type"] == "M" || $product_move["product_type"] == "E" || $product_move["product_type"] == "C"){
                            $input_product_serie = [
                                "product_id" => $product_move["product_id"],
                                "serie" => $product_move["product_serie"],
                                "status" => "N",
                                'branch_office_id' => $document->branch_office_id,
                            ];
                            if($product_move["product_serie_id"] != ""){
                                $product_serie = ProductSerie::find($product_move["product_serie_id"]);
                                $product_serie->update($input_product_serie);
                            }else{
                                $product_serie = ProductSerie::create($input_product_serie);
                            }

                            $input_product_move = [
                                'document_id' => $document->id,
                                'guide_detail_id' => $guide_detail->id,
                                'branch_office_id' => $document->branch_office_id,
                                'product_id' => $product_move["product_id"],
                                'product_serie_id' => $product_serie->id,
                                'quantity' => $product_move["quantity"],
                                'type' => "I",
                                'status' => "N",
                            ];
                        }else{
                            $input_lot = [
                                'lot' => $product_move["lot_name"],
                                'barcode' => $product_move["lot_barcode"],
                                'due_date' => $product_move["lot_due_date"],
                                'quantity' => $product_move["quantity"],
                                'remain' => $product_move["quantity"],
                                "status" => "N",
                                'product_id' => $product_move["product_id"],
                                'branch_office_id' => $document->branch_office_id,
                            ];
                            if($product_move["lot_id"] != ""){
                                $lot = Lot::find($product_move["lot_id"]);
                                $input_lot["quantity"] = $input_lot["quantity"] + $lot->quantity;
                                $input_lot["remain"] = $lot->remain;
                                $input_lot["status"] = "Y";
                                $lot->update($input_lot);
                            }else{
                                $lot = Lot::where('branch_office_id','=',$document->branch_office_id)
                                            ->where('lot','like',"'".$product_move["lot_name"]."'")
                                            ->where('barcode','like',"'".$product_move["lot_barcode"]."'")
                                            ->where('due_date','=',$product_move["lot_due_date"])
                                            ->where('product_id','=',$product_move["product_id"])
                                            ->first();
                                if(is_null($lot)){
                                    $lot = Lot::create($input_lot);
                                }else{
                                    $input_lot["quantity"] = $input_lot["quantity"] + $lot->quantity;
                                    $input_lot["remain"] = $lot->remain;
                                    $input_lot["status"] = "Y";
                                    $lot->update($input_lot);  
                                }
                            }

                            $input_product_move = [
                                'document_id' => $document->id,
                                'guide_detail_id' => $guide_detail->id,
                                'branch_office_id' => $document->branch_office_id,
                                'product_id' => $product_move["product_id"],
                                'lot_id' => $lot->id,
                                'quantity' => $product_move["quantity"],
                                'type' => "I",
                                'status' => "N",
                            ];
                        }
                        if($product_move["id"] != ""){
                            ProductMove::find($product_move["id"])->update($input_product_move);
                        }else{
                            ProductMove::create($input_product_move);
                        }

                    }
                }

            }
            $status = "Y";
            foreach ($purchase->details as $key => $value) {
                if($value->status == "N"){
                    $status = "N";
                }
            }
            $purchase->update([
                "dispatched" => $status,
            ]);



            DB::commit();

            Session::flash('success', 'Se registró correctamente');
            redirect()->route('entry-guide');


        } catch (\Exception $th) {

            DB::rollback();

            $this->emit('alert-error', [
                'title' => '¡Error!',
                'text' => 'Algo salio mal: ' . $th->getMessage(),
            ]);
        }
    }

    private function input_create_entry_guide(){
        $detail = [
            "comment" => $this->comment ? $this->comment : ""
        ];
        return [
            "type" => "C",
            "document_type_id" => $this->document_type_id,
            "document_id" => $this->document_id,
            'client_id' => $this->supplier["id"],
            'user_id' => Auth::user()->id,
            'branch_office_id' => Auth::user()->branch_office_id,
            "date_time" => $this->date_time,
            "number" => $this->number,
            'detail' => json_encode($detail),
            'dispatched' => 'N',
            'status' => 'Y'
        ];
    }



}
