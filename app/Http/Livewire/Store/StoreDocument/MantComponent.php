<?php

namespace App\Http\Livewire\Store\StoreDocument;

use App\Http\Traits\Maintenance\ProductTrait;
use App\Http\Traits\Store\StoreDocumentTrait;
use App\Models\DocumentDetail;
use App\Models\DocumentType;
use App\Models\Lot;
use App\Models\Product;
use App\Models\Reason;
use App\Models\ProductMove;
use App\Models\ProductSerie;
use App\Models\StoreDocument;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Livewire\Component;

class MantComponent extends Component
{
    use ProductTrait;
    use StoreDocumentTrait;

    protected $aditional_actions;
    protected $arr_actions;

    public $store_document;
    public $modal_document_detail = false;

    public $document_type_id, $reason_id, $number, $date_time, $comment;

    public $document_type_name;
    public $branch_office;
    public $document_types;
    public $reasons;

    public $search_product;
    public $barcode;
    public $list_search_product;
    public $shopping_car;

    public $document_detail_key;
    public $product_serie;
    public $product_lot;
    public $product_lot_id;
    public $product_barcode;
    public $product_due_date;
    public $lot_quantity;
    public $search_lot;
    public $lot_remain;

    public function hydrate()
    {
        $this->resetValidation();
    }

    public function mount(StoreDocument $store_document)
    {
        $this->store_document = $store_document;
        $this->list_search_product = [];
        $this->document_types = StoreDocument::list_document_types();
        $this->reasons = Reason::whereIn('document_type_id',[6])->orderBy('name','asc')->get();

        $this->document_type_id = $this->document_types[0]->id;
        $this->document_type_name = $this->document_types[0]->name;
        $this->date_time = date('Y-m-d');
        $this->number = $this->getNextNumber($this->document_type_id);
        $this->comment = "";
        $this->reason_id = $this->reasons[0]->id;

        $branch_office = Auth::user()->branch_office;

        $this->shopping_car = [];

        if($store_document->id != null){
            $this->document_type_id = $this->store_document->document_type_id;
            $this->document_type_name = DocumentType::find($this->store_document->document_type_id)->name;
            $this->number = $this->store_document->number;
            $this->date_time = date_format(new \DateTime($this->store_document->date_time), 'Y-m-d');
            $this->comment = $this->store_document->comment();
            $this->reason_id = $this->store_document->reason_id;

            $branch_office = $store_document->branch_office;

            $details = $store_document->details;
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
            'id' => $branch_office->id,
            'name' => $branch_office->name,
            'company_name' => $branch_office->company->business_name,
            'company_ruc' => $branch_office->company->ruc,
            'address' => $branch_office->address,
            'department_name' => $branch_office->department->name,
            'province_name' => $branch_office->province->name,
            'district_name' => $branch_office->district->name,
        ];
        $this->search_products();
    }

    public function render()
    {
        $this->load_actions();
        $arr_actions = $this->arr_actions;
        return view('livewire.store.store-document.mant-component')->with(compact('arr_actions'));
    }

    /**
     * Función para regresar
     */
    public function back_button(){
        redirect()->route('store-document');
    }

    /**
     * Función para cargar permisos de usuario
     */
    protected function load_actions(){
        $this->aditional_actions = Auth::user()->user_type->actions()
            ->whereIn('actions.code', ['create_reason'])->get();
        $this->arr_actions = [];
        foreach ($this->aditional_actions as $key => $value) {
            $this->arr_actions[] = $value->code;
        }
    }

    /**
     * Funciones de actualizacion de fields
     */
    public function updatedDocumentTypeId($value)
    {
        $document_type = DocumentType::find($value);
        if($document_type){
            $this->document_type_name = $document_type->name;
            $this->number = $this->getNextNumber($this->document_type_id);
            $this->shopping_car = [];
            $this->document_detail_key = null;
            $this->reasons = Reason::whereIn('document_type_id',[$document_type->id])->orderBy('name','asc')->get();
            $this->reason_id = $this->reasons[0]->id;
        }
    }

    /**
     * Funciones para buscar productos
     */
    public function search_products(){
        //if($this->search_product != ""){
            $this->list_search_product = $this->search_products_fast(null, $this->search_product);
        /*}else{
            $this->list_search_product = [];
        }*/
    }

    public function select_products($key){
        if(array_key_exists($key, $this->list_search_product)){
            foreach ($this->shopping_car as $key2 => $value) {
                if($value["product_id"] == $this->list_search_product[$key]["id"]){
                    return true;
                }
            }
            $this->shopping_car[] = [
                "product_id" => $this->list_search_product[$key]["id"],
                "product_name" => $this->list_search_product[$key]["name"]." ".$this->list_search_product[$key]["model"]." ".$this->list_search_product[$key]["presentation"]." ".$this->list_search_product[$key]["brand_name"],
                "product_type" => $this->list_search_product[$key]["type"],
                "unit_id" => $this->list_search_product[$key]["base_unit_id"],
                "unit_name" => $this->list_search_product[$key]["base_unit_name"],
                "units" => $this->list_search_product[$key]["units"],
                "quantity" => "0",
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
        $this->product_lot_id = 0;
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
            foreach ($this->shopping_car as $key2 => $value) {
                if($value["product_id"] == $product_search["id"]){
                    return true;
                }
            }
            $this->shopping_car[] = [
                "product_id" => $product_search["id"],
                "product_name" => $product_search["name"],
                "product_type" => $product_search["type"],
                "unit_id" => $product_search["base_unit_id"],
                "unit_name" => $product_search["base_unit_name"],
                "units" => $product_search["units"],
                "quantity" => "0",
                "product_moves" => [],
            ];
        }
    }

    /**
     * FUNCIONES PARA ASIGNAR SERIES O LOTES
     */
    public function add_product_move($key){
        $this->modal_document_detail = true;
        $this->document_detail_key = "" . $key;
        $this->clear_product_moves_data();
        $this->search_product_lot();
    }

    public function add_product_serie(){
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

            $product_serie = ProductSerie::where('serie', $this->product_serie)
                ->where('product_id', $this->shopping_car[$this->document_detail_key]["product_id"])
                ->where('branch_office_id', $this->branch_office["id"])
                ->whereNotIn('status', ['A'])
                ->where('status','=','Y')->first();

            if($this->document_type_id == 6){
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
                    "lot_barcode" => "",
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
                    "lot_barcode" => "",
                    "lot_due_date" => "",
                    "quantity" => "1",
                ];
            }


            $this->update_dispatched_quantity();
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
                if($this->product_lot == $value["lot_id"]){
                    $this->emit('alert-warning', [
                        'title' => 'Cuidado!',
                        'text' => 'Ya agregó ese lote.',
                    ]);
                    return true;
                }
            }


            if($this->document_type_id == 6){
                $lot = Lot::where('lot', $this->product_lot)
                    ->where('product_id', $this->shopping_car[$this->document_detail_key]["product_id"])
                    ->where('branch_office_id', $this->branch_office["id"])
                    ->where('due_date',$this->product_due_date)
                    ->where('status', '<>', 'A')->first();

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
                    "lot_barcode" => $this->product_barcode,
                    "lot_due_date" => $this->product_due_date,
                    "quantity" => $this->lot_quantity,
                ];
            }else{
                $lot = Lot::where('id', $this->product_lot_id)
                    ->where('product_id', $this->shopping_car[$this->document_detail_key]["product_id"])
                    ->where('branch_office_id', $this->branch_office["id"])
                    ->where('status', '<>', 'A')->first();
                    
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
                    "lot_name" => $lot->lot,
                    "lot_barcode" => $this->product_barcode,
                    "lot_due_date" => $this->product_due_date,
                    "quantity" => $this->lot_quantity,
                ];
            }

            $this->update_dispatched_quantity();
            $this->clear_product_moves_data();
        }
    }

    private function clear_product_moves_data(){
        $this->product_serie = "";
        $this->product_due_date = "";
        $this->lot_quantity = "";
        $this->product_lot = "";
        $this->product_lot_id = 0;
        $this->search_lot = "";
        $this->lot_remain = "";
    }

    public function search_product_lot(){
        $search = $this->search_lot;
        $lot = Lot::where(function($sql) use($search){
                $sql->where('lot','ilike', '%'.$search.'%')->orWhere('barcode','ilike','%'.$search.'%');
            })
                ->where('product_id', $this->shopping_car[$this->document_detail_key]["product_id"])
                ->where('branch_office_id', $this->branch_office["id"])
                ->where('remain','>',0) 
                ->where('status', '<>', 'A')->first();
        if($lot){
            $this->product_due_date = $lot->due_date;
            $this->lot_quantity = $lot->lot_quantity;
            $this->product_lot = $lot->lot;
            $this->product_lot_id = $lot->id;
            $this->product_barcode = $lot->barcode;
            $this->lot_remain = sprintf('%g', $lot->remain);
        }
    }

    public function quit_product_move($key){
        unset($this->shopping_car[$this->document_detail_key]["product_moves"][$key]);
        $this->update_dispatched_quantity();
    }

    private function update_dispatched_quantity(){
        foreach ($this->shopping_car as $key => $value) {
            $quantity = 0;
            foreach ($this->shopping_car[$key]["product_moves"] as $product_move) {
                $quantity = $quantity + $product_move["quantity"];
            }
            $this->shopping_car[$key]["quantity"] = $quantity;
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
        if($this->reason_id == null){
            $this->emit('alert-error', [
                'title' => 'Error!',
                'text' => 'No se ha seleccionado un motivo',
            ]);
            return false;
        }
        if(!$this->validate_product_moves()){
            return true;
        }
        DB::beginTransaction();
        try {

            $document = StoreDocument::create($this->input_create_entry_guide());

            foreach ($this->shopping_car as $key => $item) {
                if(count($item["product_moves"]) > 0){
                    $input_detail = [
                        'document_id' => $document->id,
                        'product_id' => $item["product_id"],
                        'unit_id' => $item["unit_id"],
                        'quantity' => $item["quantity"],
                        'dispatched_quantity' => 0,
                        'status' => 'N'
                    ];
                    $guide_detail = DocumentDetail::create($input_detail);

                    foreach ($item["product_moves"] as $key => $product_move) {
                        if($product_move["product_type"] == "M" || $product_move["product_type"] == "E" || $product_move["product_type"] == "C"){
                            $type = "E";
                            if($this->document_type_id == 6){
                                $type = "I";

                                if($product_move["product_serie_id"] == ""){
                                    $input_product_serie = [
                                        "product_id" => $product_move["product_id"],
                                        "serie" => $product_move["product_serie"],
                                        "status" => "N",
                                        'branch_office_id' => $document->branch_office_id,
                                    ];
                                    $product_serie = ProductSerie::create($input_product_serie);
                                }else{
                                    $product_serie = ProductSerie::find($product_move["product_serie_id"]);
                                }
                            }else{
                                $product_serie = ProductSerie::find($product_move["product_serie_id"]);
                            }

                            $input_product_move = [
                                'document_id' => $document->id,
                                'guide_detail_id' => $guide_detail->id,
                                'branch_office_id' => $document->branch_office_id,
                                'product_id' => $product_move["product_id"],
                                'product_serie_id' => $product_serie->id,
                                'quantity' => $product_move["quantity"],
                                'type' => $type,
                                'status' => "N",
                            ];
                        }else{
                            $type = "E";
                            if($this->document_type_id == 6){
                                $type = "I";

                                if($product_move["lot_id"] == ""){
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
                                }else{
                                    $lot = Lot::find($product_move["lot_id"]);
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
                                    $input_lot["quantity"] = $input_lot["quantity"] + $lot->quantity;
                                    $input_lot["remain"] = $lot->remain;
                                    $input_lot["status"] = "Y";
                                    $lot->update($input_lot); 
                                }
                            }else{
                                $lot = Lot::find($product_move["lot_id"]);
                            }

                            $input_product_move = [
                                'document_id' => $document->id,
                                'guide_detail_id' => $guide_detail->id,
                                'branch_office_id' => $document->branch_office_id,
                                'product_id' => $product_move["product_id"],
                                'lot_id' => $lot->id,
                                'quantity' => $product_move["quantity"],
                                'type' => $type,
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


            DB::commit();

            Session::flash('success', 'Se registró correctamente');
            redirect()->route('store-document');


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
            'user_id' => Auth::user()->id,
            'branch_office_id' => Auth::user()->branch_office_id,
            "date_time" => $this->date_time." ".date("H:i:s"),
            "number" => $this->getNextNumber($this->document_type_id),
            'detail' => json_encode($detail),
            'dispatched' => 'N',
            'status' => 'Y',
            'client_id' => Auth::user()->person_id,
            'reason_id' => $this->reason_id,
        ];
    }
}
