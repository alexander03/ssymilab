<?php

namespace App\Http\Livewire\Store\ReferralGuide;

use App\Http\Traits\Maintenance\PersonTrait;
use App\Http\Traits\Maintenance\ProductTrait;
use App\Http\Traits\Store\ReferralGuideTrait;
use App\Models\DocumentDetail;
use App\Models\Lot;
use App\Models\Person;
use App\Models\Product;
use App\Models\ProductMove;
use App\Models\ProductSerie;
use App\Models\ReferralGuide;
use App\Models\Role;
use App\Models\District;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Livewire\Component;

class MantComponent extends Component
{
    use ProductTrait;
    use ReferralGuideTrait;
    use PersonTrait;

    protected $aditional_actions;
    protected $arr_actions;

    public $referral_guide;
    public $modal_document_detail = false;
    public $modal_supplier = false;

    public $reason_id, $number, $date_time, $comment, $client_id, $total_weight, $transport_plate,$transport_licencia;
    public $transfer_date, $start_address, $final_address, $number_packages, $orden_compra, $transbordo, $modalidad;

    public $branch_office;
    public $reasons;
    public $supplier;

    public $search_product;
    public $barcode;
    public $list_search_product;
    public $shopping_car;

    public $search_person;
    public $search_list_people;
    public $search_list_transport;
    public $search_transport;
    public $search_list_district;
    public $search_list_district2;
    public $search_start;
    public $search_arrival;
    public $district_start;
    public $district_arrival;

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

    public function mount(ReferralGuide $referral_guide)
    {
        $this->referral_guide = $referral_guide;
        $this->search_list_people = [];
        $this->list_search_product = [];
        $this->search_list_transport = [];
        $this->list_search_product = [];
        $this->search_list_document = [];
        $this->search_list_district = [];
        $this->search_list_district2 = [];
        $this->reasons = ReferralGuide::list_reasons();

        $this->reason_id = $this->reasons[0]->id;
        $this->date_time = date('Y-m-d');
        $this->number = $this->getNextNumberGuide();
        $this->comment = "";
        $this->modalidad = "01";
        $this->transbordo = "";

        $branch_office = Auth::user()->branch_office;
        if($branch_office->id==3){
            $this->start_address = "AV. INFANCIA NRO. 538";
            $district = District::where('code','like','080108')->first();
        }else{
            $this->start_address = "CAL.MIGUEL GRAU NRO. 403 (FRENTE A COMERCIAL TIMO)";
            $district = District::where('code','like','110201')->first();
        }
        $this->district_start = [
                "code" => $district->code,
                "name_complete" => $district->department->name.' - '.$district->province->name.' - '.$district->name,
        ];
        $this->search_start = $district->department->name.' - '.$district->province->name.' - '.$district->name;
        $supplier = null;
        $transport = null;

        $this->shopping_car = [];

        if($referral_guide->id != null){
            $this->reason_id = $this->referral_guide->reason_id;
            $this->client_id = $this->purchase->client_id;
            $this->number = $this->referral_guide->number;
            $this->date_time = date_format(new \DateTime($this->referral_guide->date_time), 'Y-m-d');
            $this->comment = $this->referral_guide->comment();

            $branch_office = $referral_guide->branch_office;
            $supplier = $referral_guide->client;

            $details = $referral_guide->details;
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
        $this->supplier = [
            'id' => ($supplier) ? $supplier->id : "",
            'type' => ($supplier) ? $supplier->type : "6",
            'name_complete' => ($supplier) ? $supplier->getNameComplete() : "",
            'document_number' => ($supplier) ? $supplier->getDocumentNumber() : "",
        ];
        $this->transport = [
            'id' => ($transport) ? $transport->id : "",
            'type' => ($transport) ? $transport->type : "6",
            'name_complete' => ($transport) ? $transport->getNameComplete() : "",
            'document_number' => ($transport) ? $transport->getDocumentNumber() : "",
        ];
        $this->search_transport = $this->transport["name_complete"];
        $this->search_person = $this->supplier["name_complete"];
        $this->search_products();
    }

    public function render()
    {
        $this->load_actions();
        $arr_actions = $this->arr_actions;
        $person = new Person();
        $role_id = Role::where('code', 'C')->first()->id;
        return view('livewire.store.referral-guide.mant-component')->with(compact('person', 'role_id', 'arr_actions'));
    }

    /**
     * Función para regresar
     */
    public function back_button(){
        redirect()->route('referral-guide');
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
     * Funciones para filtro de Personas
     */
    public function updatingSearchPerson($value)
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
            $this->supplier = [
                "id" => $person->id,
                'type' => $person->type,
                "name_complete" => $person->getNameComplete(),
                "document_number" => $person->getDocumentNumber(),
                "address" => $person->address,
            ];
            $this->search_person = $this->supplier["name_complete"];
            $this->final_address = $this->supplier["address"];
        }
    }

    public function updatingSearchTransport($value)
    {
        if($value != ""){
            $this->search_list_transport = $this->search_autocomplete($value, "T");
        }else{
            $this->search_list_transport = [];
        }
    }

    public function select_active_transport($person_id){
        $person = Person::find($person_id);
        if($person){
            $this->transport = [
                "id" => $person->id,
                'type' => $person->type,
                "name_complete" => $person->getNameComplete(),
                "document_number" => $person->getDocumentNumber()
            ];
            $this->search_transport = $this->transport["name_complete"];
        }
    }

    public function updatingSearchStart($value)
    {
        if($value != ""){
            $this->search_list_district = $this->search_autocomplete_district($value);
        }else{
            $this->search_list_district = [];
        }
    }

    public function select_active_start($district_id){
        $district = District::where('code','like',$district_id)->first();
        $this->district_start = [
                "code" => $district->code,
                "name_complete" => $district->department->name.' - '.$district->province->name.' - '.$district->name,
        ];
        $this->search_start = $district->department->name.' - '.$district->province->name.' - '.$district->name;
    }

    public function updatingSearchArrival($value)
    {
        if($value != ""){
            $this->search_list_district2 = $this->search_autocomplete_district($value);
        }else{
            $this->search_list_district2 = [];
        }
    }

    public function select_active_arrival($district_id){
        $district = District::where('code','like',$district_id)->first();
        $this->district_arrival = [
                "code" => $district->code,
                "name_complete" => $district->department->name.' - '.$district->province->name.' - '.$district->name,
        ];
        $this->search_arrival = $district->department->name.' - '.$district->province->name.' - '.$district->name;
    }

    /**
     * Funciones para buscar productos
     */
    public function search_products(){
        //if($this->search_product != ""){
            $this->list_search_product = $this->search_products_fast(null, $this->search_product);
        //}else{
            //$this->list_search_product = [];
        //}
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
                "product_name" => $this->list_search_product[$key]["name"].' '.$this->list_search_product[$key]["model"].' '.$this->list_search_product[$key]["presentation"].' '.$this->list_search_product[$key]["brand_name"],
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
                ->where('status', '<>', 'A')->first();

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


            $this->update_dispatched_quantity();
            $this->product_serie = "";
            $this->product_due_date = "";
            $this->lot_quantity = "";
            $this->product_lot = "";
            $this->product_lot_id = 0;
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

            $lot = Lot::where('id', $this->product_lot_id)
                ->where('product_id', $this->shopping_car[$this->document_detail_key]["product_id"])
                ->where('branch_office_id', $this->branch_office["id"])
                ->where('remain','>',0)
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
                if(!$lot || $lot->status != "Y" || ($lot->remain - $this->lot_quantity) < 0){
                    $this->emit('alert-warning', [
                        'title' => 'Cuidado!',
                        'text' => 'El lote no puede ser disminuido.'.$lot->id,
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

            $this->update_dispatched_quantity();
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
                ->where('remain','>',0) 
                ->where('status', '<>', 'A')
                ->where('status','<>','N')->first();

        if($lot){
            $this->product_due_date = $lot->due_date;
            $this->lot_quantity = $lot->lot_quantity;
            $this->product_lot = $lot->lot;
            $this->product_barcode = $lot->barcode;
            $this->lot_remain = sprintf('%g', $lot->remain);
            $this->product_lot_id = $lot->id;
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

        if(!$this->validate_product_moves()){
            return true;
        }
        if($this->modalidad=="02" && $this->transport_licencia==""){
            $this->emit('alert-error', [
                'title' => 'Error!',
                'text' => 'No ha registrado numero de licencia.',
            ]);
            return true;
        }
        DB::beginTransaction();
        try {

            $document = ReferralGuide::create($this->input_create_referral_guide());

            foreach ($this->shopping_car as $key => $item) {
                if(count($item["product_moves"]) > 0){
                    $detail2 = [
                        "producto" => $item["product_name"],
                    ];

                    $input_detail = [
                        'document_id' => $document->id,
                        'product_id' => $item["product_id"],
                        'unit_id' => $item["unit_id"],
                        'quantity' => $item["quantity"],
                        'dispatched_quantity' => 0,
                        'status' => 'N',
                        'detail' => json_encode($detail2)
                    ];
                    $guide_detail = DocumentDetail::create($input_detail);

                    foreach ($item["product_moves"] as $key => $product_move) {
                        if($product_move["product_type"] == "M" || $product_move["product_type"] == "E" || $product_move["product_type"] == "C"){
                            $type = "E";
                            if(false){
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
                            if(false){
                                $type = "I";

                                if($product_move["lot_id"] == ""){
                                    $input_lot = [
                                        'lot' => $product_move["lot_name"],
                                        'due_date' => $product_move["lot_due_date"],
                                        'quantity' => $product_move["quantity"],
                                        'remain' => $product_move["quantity"],
                                        "status" => "N",
                                        'product_id' => $product_move["product_id"],
                                        'branch_office_id' => $document->branch_office_id,
                                    ];
                                    $lot = Lot::create($input_lot);
                                }else{
                                    $lot = Lot::find($product_move["lot_id"]);
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
            Session::flash('id_guia', $document->id);
            redirect()->route('referral-guide');


        } catch (\Exception $th) {

            DB::rollback();

            $this->emit('alert-error', [
                'title' => '¡Error!',
                'text' => 'Algo salio mal: ' . $th->getMessage(),
            ]);
        }
    }

    private function input_create_referral_guide(){
        $detail = [
            "comment" => $this->comment ? $this->comment : "",
            "transfer_date" => $this->transfer_date ? $this->transfer_date : "",
            "start_address" => $this->start_address ? $this->start_address : "",
            "final_address" => $this->final_address ? $this->final_address : "",
            "transport_plate" => $this->transport_plate,
            "transport_licencia" => $this->transport_licencia,
            "number_packages" => $this->number_packages,
            "orden_compra" => $this->orden_compra,
            "transbordo" => $this->transbordo,
            "modalidad" => $this->modalidad,
        ];
        return [
            "type" => "C",
            "reason_id" => $this->reason_id,
            'client_id' => $this->supplier["id"],
            'shipper_id' => $this->transport["id"],
            'ubigeo_start_code' => $this->district_start["code"],
            'ubige_end_code' => $this->district_arrival["code"],
            'weight' => $this->total_weight,
            'user_id' => Auth::user()->id,
            'branch_office_id' => Auth::user()->branch_office_id,
            "date_time" => $this->date_time." ".date("H:i:s"),
            "number" => $this->getNextNumberGuide(),
            'detail' => json_encode($detail),
            'schedule_time' => $this->transfer_date ? $this->transfer_date : "",
            'dispatched' => 'N',
            'status' => 'Y'
        ];
    }
}
