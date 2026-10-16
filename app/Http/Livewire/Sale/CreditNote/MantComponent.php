<?php

namespace App\Http\Livewire\Sale\Creditnote;

use App\Http\LivewireRequest\Purchase\Purchase\PurchaseRequest;
use App\Http\Traits\Maintenance\PersonTrait;
use App\Http\Traits\Maintenance\ProductTrait;
use App\Http\Traits\Sale\SaleTrait;
use App\Http\Traits\Sale\CreditNoteTrait;
use App\Http\Traits\Store\LotTrait;
use App\Models\Card;
use App\Models\Currency;
use App\Models\DocumentDetail;
use App\Models\DocumentPayment;
use App\Models\DocumentType;
use App\Models\ExchangeRate;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Person;
use App\Models\PersonProduct;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\Proforma;
use App\Models\PurchaseRequirement;
use App\Models\Role;
use App\Models\Sale;
use App\Models\CreditNote;
use App\Models\Lot;
use App\Models\Reason;
use App\Models\TransferMethod;
use App\Models\ProductMove;
use App\Models\ProductSerie;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Livewire\Component;


class MantComponent extends Component
{
    use PersonTrait;
    use ProductTrait;
    use SaleTrait;
    use LotTrait;
    use CreditNoteTrait;

    protected $aditional_actions;
    protected $arr_actions;

    public $creditnote;

    public $modal_document_detail = false;
    public $modal_supplier = false;

    public $document_type_id, $client_id, $document_id, $currency_id, $type, $declaration, $schedule_time, $number, $date_time, $total, $exchange_rate, $pending_amount, $comment,$order,$reason_id;
    public $paided_amount;

    public $document_type_name;
    public $branch_office;
    public $supplier;
    public $personal;
    public $currency;
    public $document_types, $currencies;
    public $payment_methods;
    public $payments;
    public $reasons;
    public $document_payments;

    public $search_document;
    public $search_list_document;

    public $search_document_sale;
    public $search_list_document_sale;

    public $search_person;
    public $search_list_people;

    public $search_personal;
    public $search_list_personal;

    public $search_list_lot;
    public $search_list_serie;

    public $search_product;
    public $search_serie;
    public $barcode;
    public $list_search_product;
    public $shopping_car;

    public $document_detail_key;
    public $product_serie;
    public $product_lot;
    public $product_barcode;
    public $product_due_date;
    public $lot_quantity;
    public $search_lot;
    public $lot_remain;

    protected $listeners = [
        'toggle-modal-supplier' => 'toggle_supplier',
    ];

    public function hydrate()
    {
        $this->resetValidation();
    }

    public function mount(CreditNote $creditnote)
    {
        $this->creditnote = $creditnote;
        $this->search_list_people = [];
        $this->search_list_personal = [];
        $this->search_list_lot = [];
        $this->search_list_serie = [];
        $this->search_list_document = [];
        $this->search_list_document_sale = [];
        $this->list_search_product = [];
        $this->document_payments = [];
        $this->document_types = CreditNote::list_document_types();
        $this->currencies = Currency::order_currency()->get();
        $this->reasons = Reason::whereIn('document_type_id',[17])->orderBy('name','asc')->get();

        $this->document_type_id = $this->document_types[0]->id;
        $this->document_type_name = $this->document_types[0]->name;
        $this->currency_id = $this->currencies[0]->id;
        $this->type = "D";
        $this->declaration = "C";
        $this->date_time = date('Y-m-d');
        $this->total = 0;
        $this->exchange_rate = "1";
        $this->pending_amount = 0;
        $this->comment = "";
        $this->number = $this->getNextNumberCreditNote($this->document_types[0]->id);
        $this->reason_id = 29;

        $branch_office = Auth::user()->branch_office;
        $supplier = null;
        $personal = null;

        $this->shopping_car = [];

        $this->currency = [
            "symbol" => $this->currencies[0]->symbol,
            "code" => $this->currencies[0]->code,
            "name" => $this->currencies[0]->name,
            "id" => $this->currencies[0]->id,
        ];

        


        if($creditnote->id != null){
            $this->document_type_id = $this->creditnote->document_type_id;
            $this->client_id = $this->creditnote->client_id;
            $this->document_id = $this->creditnote->document_id;
            $this->document_type_name = DocumentType::find($this->creditnote->document_id)->name;
            $this->currency_id = $this->creditnote->currency_id;
            $this->type = $this->creditnote->type;
            $this->schedule_time = $this->creditnote->schedule_time;
            $this->number = $this->creditnote->number;
            $this->reason_id = $this->creditnote->reason_id;
            $this->date_time = date_format(new \DateTime($this->creditnote->date_time), 'Y-m-d');
            $this->total = $this->creditnote->total;
            $this->exchange_rate = $this->creditnote->exchange_rate;
            $this->pending_amount = $this->creditnote->pending_amount;
            $this->comment = $this->creditnote->comment();

            $this->currency = [
                "symbol" => $this->creditnote->currency->symbol,
                "code" => $this->creditnote->currency->code,
                "name" => $this->creditnote->currency->name,
                "id" => $this->creditnote->currency->id,
            ];

            $branch_office = $creditnote->branch_office;
            $supplier = $creditnote->client;
            $personal = $creditnote->person;

            $details = $creditnote->details;
            foreach ($details as $key => $value) {
                $product = Product::find($value["product_id"]);
                if($product){
                    $this->shopping_car[] = [
                        "product_id" => $value["product_id"],
                        "product_name" => $product->name,
                        "product_name_original" => $product->name.' - '.$product->presentation,
                        "product_type" => $product->type,
                        "unit_id" => $value["unit_id"],
                        "currency_id" => $value["currency_id"],
                        "units" => $product->all_only_units,
                        "quantity" => sprintf('%g', $value["quantity"]),
                        "sell_price" => sprintf('%g', $value["sell_price"])
                    ];
                }
            }
            $this->updatedShoppingCar();
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
        $this->personal = [
            'id' => ($personal) ? $personal->id : "",
            'type' => ($personal) ? $personal->type : "6",
            'name_complete' => ($personal) ? $personal->getNameComplete() : "",
            'document_number' => ($personal) ? $personal->code : "",
        ];
        $this->search_person = $this->supplier["name_complete"];
        $this->search_personal = $this->personal["name_complete"];

    }

    public function render()
    {
        $this->load_actions();
        $arr_actions = $this->arr_actions;

        return view('livewire.sale.creditnote.mant-component')->with(compact('arr_actions'));
    }

    /**
     * Función para regresar
     */
    public function back_button(){
        redirect()->route('creditnote');
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

    public function updatedShoppingCar()
    {
        $total = 0;
        foreach ($this->shopping_car as $key => $value) {
            if(!$value["quantity"] || trim($value["quantity"]) == "" || trim($value["quantity"]) <= 0 ){
                $value["quantity"] = "1";
                $this->shopping_car[$key]["quantity"] = "1";
            }
            if(!$value["sell_price"] || trim($value["sell_price"]) == "" || trim($value["sell_price"]) < 0 ){
                $value["sell_price"] = "0.00";
                $this->shopping_car[$key]["sell_price"] = "0.00";
            }
            $total = $total + $value["quantity"] * $value["sell_price"];
        }
        $this->total = $total;
        $this->updatedType();
    }

    public function updatingDocumentTypeId($value)
    {
        $document_type = DocumentType::find($value);
        if($document_type){
            $this->document_type_name = $document_type->name;
            $this->number = $this->getNextNumberSale($document_type->id);
        }
    }

    /**
     * Funciones para filtro de Personas
     */
    public function updatingSearchPerson($value)
    {
        if($value != ""){
            $this->search_list_people = $this->search_autocomplete($value,null, ['C','D']);
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
     * Funciones para filtro de Vendedor
     */
    public function updatingSearchPersonal($value)
    {
        if($value != ""){
            $this->search_list_personal = $this->search_autocomplete2($value, ["A","S"]);
        }else{
            $this->search_list_personal = [];
        }
    }

    public function select_active_personal($person_id){
        $person2 = Person::find($person_id);
        if($person2){
            $this->personal = [
                "id" => $person2->id,
                'type' => $person2->type,
                "name_complete" => $person2->getNameComplete(),
                "document_number" => $person2->code,
            ];
            $this->search_personal = $this->personal["name_complete"];
        }
    }

    /**
     * Funciones para filtro de Lote
     */
    public function updatingSearchLot($value)
    {
        if($value != ""){
            $this->search_list_lot = $this->search_autocompleteLotCreditNote($value, $this->shopping_car[$this->document_detail_key]["product_id"]);
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
     * Funciones para filtro de Sale
     */
    public function updatingSearchDocumentSale($value)
    {
        if($value != ""){
            $this->search_list_document_sale = $this->search_sale_autocomplete_creditnote($value);
        }else{
            $this->search_list_document_sale = [];
        }
    }

    public function select_active_document_sale($document_id){
        $document = Sale::find($document_id);
        if($document){
            $this->document_id = $document->id;
            $this->search_document_sale = $document->number;
            $this->number = $this->getNextNumberCreditNote($document->document_type_id);
            $this->shopping_car = [];
            $details = $document->details;
            foreach ($details as $key => $value) {
                $product = Product::find($value["product_id"]);
                if($product){
                    $this->shopping_car[] = [
                        "product_id" => $value["product_id"],
                        "product_name" => $product->name,
                        "product_type" => $product->type,
                        "product_name_original" => $product->name.' - '.$product->presentation,
                        "unit_id" => $value["unit_id"],
                        "currency_id" => $value["currency_id"],
                        "units" => $product->all_only_units,
                        "quantity" => sprintf('%g', $value["quantity"]),
                        "sell_price" => "0.00",
                        "product_moves" => [],
                    ];
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
            $this->updatedShoppingCar();
        }
    }

    public function quit_document_sale(){
        $this->document_id = null;
        $this->search_document_sale = "";
        $this->shopping_car = [];
        $this->supplier = [
            'id' => "",
            'type' => "6",
            'name_complete' => "",
            'document_number' => "",
        ];
        $this->search_person = $this->supplier["name_complete"];
        $this->updatedShoppingCar();
    }


    /**
     * Funciones para buscar productos
     */
    public function search_products(){
        if($this->search_product != ""){
            $this->list_search_product = $this->search_products_fast(null, $this->search_product);
            //dd($this->list_search_product);
        }else{
            $this->list_search_product = [];
        }
    }

    public function select_products($key){
        if(array_key_exists($key, $this->list_search_product)){
            foreach ($this->shopping_car as $key2 => $value) {
                if($value["product_id"] == $this->list_search_product[$key]["id"]){
                    $this->shopping_car[$key2] = [
                        "product_id" => $this->list_search_product[$key]["id"],
                        "product_name" => $this->list_search_product[$key]["name"],
                        "product_type" => $this->list_search_product[$key]["type"],
                        "product_name_original" => $this->list_search_product[$key]["name"].' '.$this->list_search_product[$key]["model"].' - '.$this->list_search_product[$key]["presentation"],
                        "unit_id" => $this->list_search_product[$key]["base_unit_id"],
                        "currency_id" => $this->shopping_car[$key2]["currency_id"],
                        "units" => $this->list_search_product[$key]["units"],
                        "quantity" => $this->shopping_car[$key2]["quantity"] + 1,
                        "sell_price" => $this->shopping_car[$key2]["sell_price"],
                        "product_moves" => $this->shopping_car[$key2]["product_moves"],
                    ];
                    $this->updatedShoppingCar();
                    return true;
                }
            }
            if(count($this->list_search_product[$key]["sell_price"])==1){
                $price = $this->list_search_product[$key]["sell_price"][0];
            }else{
                foreach ($this->list_search_product[$key]["units"] as $k => $v) {
                    if($v["id"]==$this->list_search_product[$key]["base_unit_id"]){
                        $price = $v["sell_price"];
                    }
                }
            }
            if($this->supplier["id"]!=""){
                $dataPrice = PersonProduct::where('person_id',$this->supplier["id"])->get();
                if(!is_null($dataPrice)){
                    foreach ($dataPrice as $k => $v) {
                        if($v->product_id==$this->list_search_product[$key]["id"]){
                            $price = $v->price;
                        }
                    }
                }
            }

            $this->shopping_car[] = [
                "product_id" => $this->list_search_product[$key]["id"],
                "product_name" => $this->list_search_product[$key]["name"],
                "product_type" => $this->list_search_product[$key]["type"],
                "product_name_original" => $this->list_search_product[$key]["name"].' '.$this->list_search_product[$key]["model"].' - '.$this->list_search_product[$key]["presentation"],
                "unit_id" => $this->list_search_product[$key]["base_unit_id"],
                "currency_id" => $this->currency_id,
                "units" => $this->list_search_product[$key]["units"],
                "quantity" => 1,
                "sell_price" => $price,
                "product_moves" => [],
            ];            
            $this->updatedShoppingCar();

            $focus_quantity_id = "quantity_".$this->list_search_product[$key]["id"];
            $this->emit('focus', [
               'id' => $focus_quantity_id
            ]);
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
        $this->updatedShoppingCar();
    }

    public function changeSelectUnit($value,$key)
    {
        foreach ($this->shopping_car[$key]["units"] as $k => $v) {
            if($v["id"]==$value){
                $this->shopping_car[$key]["sell_price"] = $v["sell_price"];
            }
        }
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
                        "product_name_original" => $product_search["name"].' - '.$product_search["presentation"],
                        "product_name" => $product_search["name"],
                        "product_type" => $product_search["type"],
                        "unit_id" => $product_search["base_unit_id"],
                        "currency_id" => $this->shopping_car[$key2]["currency_id"],
                        "units" => $product_search["units"],
                        "quantity" => $this->shopping_car[$key2]["quantity"] + 1,
                        "sell_price" => $this->shopping_car[$key2]["sell_price"],
                    ];
                    $this->updatedShoppingCar();
                    return true;
                }
            }
            $this->shopping_car[] = [
                "product_id" => $product_search["id"],
                "product_name_original" => $product_search["name"].' - '.$product_search["presentation"],
                "product_name" => $product_search["name"],
                "product_type" => $product_search["type"],
                "unit_id" => $product_search["base_unit_id"],
                "currency_id" => $this->currency_id,
                "units" => $product_search["units"],
                "quantity" => 1,
                "sell_price" => "0.00",
            ];
            $this->updatedShoppingCar();
        }
    }


    /**
     * Función para guardar
     */
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

            $document_input = $this->input_create_creditnote();

            $document = Creditnote::create($document_input);

            $requirement = PurchaseRequirement::find($this->document_id);
            if($requirement){
                $requirement->update([
                    "document_id" => $document->id,
                    "status" => 'Y'
                ]);
            }


            foreach ($this->shopping_car as $key => $item) {
                $detail2 = [
                        "producto" => $item["product_name_original"],
                    ];

                $input_detail = [
                    'document_id' => $document->id,
                    'product_id' => $item["product_id"],
                    'unit_id' => $item["unit_id"],
                    'currency_id' => $this->currency_id,
                    'quantity' => $item["quantity"],
                    'sell_price' => $item["sell_price"],
                    'subtotal' => $item["quantity"]*$item["sell_price"],
                    'dispatched_quantity' => 0,
                    'status' => 'N',
                    'detail' => json_encode($detail2),
                ];
                $guide_detail = DocumentDetail::create($input_detail);

                foreach ($item["product_moves"] as $key => $product_move) {
                    if($product_move["product_type"] == "M" || $product_move["product_type"] == "E" || $product_move["product_type"] == "C"){
                        $type = "I";
                        if(true){
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
                            'document_detail_id' => $guide_detail->id,
                            'branch_office_id' => $document->branch_office_id,
                            'product_id' => $product_move["product_id"],
                            'product_serie_id' => $product_serie->id,
                            'quantity' => $product_move["quantity"],
                            'type' => $type,
                            'status' => "P",
                        ];
                    }else{
                        $type = "I";
                        if(true){
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
                            'document_detail_id' => $guide_detail->id,
                            'branch_office_id' => $document->branch_office_id,
                            'product_id' => $product_move["product_id"],
                            'lot_id' => $lot->id,
                            'quantity' => $product_move["quantity"],
                            'type' => $type,
                            'status' => "P",
                        ];
                    }
                    if($product_move["id"] != ""){
                        ProductMove::find($product_move["id"])->update($input_product_move);
                    }else{
                        ProductMove::create($input_product_move);
                    }

                }
            }

            DB::commit();

            Session::flash('success', 'Se registró correctamente');
            Session::flash('id_nota', $document->id);
            Session::flash('tipodocumento', ($document->document_type_id==13?"Factura":"Boleta"));
            redirect()->route('creditnote');


        } catch (\Exception $th) {
            DB::rollback();
            $this->emit('alert-error', [
                'title' => '¡Error!',
                'text' => 'Algo salio mal: ' . $th->getMessage(),
            ]);
        }
    }

    private function inputs_validation(){
        return [
            "document_type_id" => $this->document_type_id,
            "client_id" => $this->supplier["id"],
            "person_id" => $this->personal["id"],
            "document_id" => $this->document_id,
            "currency_id" => $this->currency_id,
            "type" => $this->type,
            "date_time" => $this->date_time,
            "number" => $this->number,
            "total" => $this->total,
            "exchange_rate" => $this->exchange_rate,
            "pending_amount" => $this->pending_amount,
            "comment" => $this->comment,
            "shopping_car" => $this->shopping_car,
        ];
    }

    private function input_create_creditnote(){
        $detail = [
            "comment" => $this->comment ? $this->comment : ""
        ];
        $person = Person::find($this->personal["id"]);
        if($this->reason_id==33 || $this->reason_id==32 || $this->reason_id==31){//descuentos
            $dispatched='Y';
        }else{
            $dispatched='N';
        }
        return [
            "type" => $this->type,
            "document_type_id" => $this->document_type_id,
            "document_id" => $this->document_id,
            'client_id' => $this->supplier["id"],
            'person_id' => $this->personal["id"],
            'supervisor_id' => $person->supervisor_id,
            'user_id' => Auth::user()->id,
            "currency_id" => $this->currency_id,
            'branch_office_id' => Auth::user()->branch_office_id,
            "date_time" => $this->date_time." ".date("H:i:s"),
            "number" => $this->number,
            "total" => $this->total,
            "exchange_rate" => $this->exchange_rate,
            "pending_amount" => $this->pending_amount,
            'detail' => json_encode($detail),
            'dispatched' => $dispatched,
            'reason_id' => $this->reason_id,
        ];
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
        if($value != ""){
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
        }else{
            $this->search_list_serie = [];
        }
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
                if(!$product_serie){
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
                /*if(!$lot || $lot->status != "Y" || $lot->remain - $this->lot_quantity < 0){
                    $this->emit('alert-warning', [
                        'title' => 'Cuidado!',
                        'text' => 'El lote no puede ser disminuido.',
                    ]);
                    return true;
                }*/
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

    private function update_dispatched_quantity(){
        foreach ($this->shopping_car as $key => $value) {
            $quantity = 0;
            foreach ($this->shopping_car[$key]["product_moves"] as $product_move) {
                $quantity = $quantity + $product_move["quantity"];
            }
            if($this->shopping_car[$key]["product_type"]!="R"){//si no es reactivo
                $this->shopping_car[$key]["quantity"] = $quantity;
            }
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
}
