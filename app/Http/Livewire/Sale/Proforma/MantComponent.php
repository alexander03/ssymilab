<?php

namespace App\Http\Livewire\Sale\Proforma;

use App\Http\Traits\Maintenance\PersonTrait;
use App\Http\Traits\Maintenance\ProductTrait;
use App\Http\Traits\Sale\ProformaTrait;
use App\Models\Currency;
use App\Models\DocumentDetail;
use App\Models\Person;
use App\Models\Product;
use App\Models\Proforma;
use App\Models\Role;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Livewire\Component;

class MantComponent extends Component
{
    use ProformaTrait;
    use PersonTrait;
    use ProductTrait;

    protected $aditional_actions;
    protected $arr_actions;

    public $proforma;

    public $modal_supplier = false;

    public $client_id, $currency_id, $number, $date_time, $total, $comment;
    public $subtotal = 0;
    public $discount = 0;

    public $branch_office;
    public $supplier;
    public $currencies;
    public $currency;

    public $search_person;
    public $search_list_people;

    public $search_product;
    public $barcode;
    public $list_search_product;
    public $shopping_car;
    public $validez;
    public $condicion;

    protected $listeners = [
        'toggle-modal-supplier' => 'toggle_supplier',
    ];

    public function mount(Proforma $proforma)
    {
        $this->proforma = $proforma;
        $this->search_list_people = [];
        $this->currencies = Currency::order_currency()->get();

        $this->currency_id = $this->currencies[0]->id;

        $this->currency = [
            "symbol" => $this->currencies[0]->symbol,
            "code" => $this->currencies[0]->code,
            "name" => $this->currencies[0]->name,
            "id" => $this->currencies[0]->id,
        ];

        if($proforma->id != null){
            $details = ($this->proforma->detail && trim($this->proforma->detail) != "") ? json_decode($this->proforma->detail, true) : [];
            $this->client_id = $this->proforma->client_id;
            $this->currency_id = $this->proforma->currency_id;
            $this->total = $this->proforma->total;
            $this->discount = $this->proforma->discount ? $this->proforma->discount + 0 : 0;
            $this->comment = (array_key_exists('comment', $details)) ? $details["comment"] : "";
            $this->validez = (array_key_exists('validez', $details)) ? $details["validez"] : "";
            $this->condicion = (array_key_exists('condicion', $details)) ? $details["condicion"] : "";
            $this->number = $this->proforma->number;
            $this->date_time = date_format(new \DateTime($this->proforma->date_time), 'Y-m-d');

            $branch_office = $proforma->branch_office;
            $supplier = $proforma->client;
            $details = $proforma->details;

            $this->currency = [
                "symbol" => $this->proforma->currency->symbol,
                "code" => $this->proforma->currency->code,
                "name" => $this->proforma->currency->name,
                "id" => $this->proforma->currency->id,
            ];

            foreach ($details as $key => $value) {
                $product = Product::find($value["product_id"]);
                $details = ($value->detail && trim($value->detail) != "") ? json_decode($value->detail, true) : [];
                if($product){
                    $this->shopping_car[] = [
                        "product_id" => $value["product_id"],
                        "product_name" => $product->name,
                        "presentation" => $product->presentation,
                        "unit_id" => $value["unit_id"],
                        "currency_id" => $value["currency_id"],
                        "units" => $product->all_only_units,
                        "delivery_times" => $product->delivery_times,
                        "delivery_time" => (array_key_exists('delivery_time', $details)) ? $details["delivery_time"] : "",
                        "due_dates" => $product->all_due_dates,
                        "due_date" => (array_key_exists('due_date', $details)) ? $details["due_date"] : "",
                        "quantity" => sprintf('%g', $value["quantity"]),
                        "sell_price" => sprintf('%g', $value["sell_price"]),
                        "sell_price_min" => sprintf('%g', $value["sell_price"])
                    ];
                }
            }

        }else{
            $this->total = 0;
            $this->date_time = date('Y-m-d');
            $this->number = $this->getNextNumber();

            $branch_office = Auth::user()->branch_office;
            $supplier = null;
            $this->shopping_car = [];
        }
        $this->list_search_product = [];
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
        $this->search_products();
        $this->calculate_total();

    }

    public function render()
    {
        $this->load_actions();
        $arr_actions = $this->arr_actions;

        $person = new Person();
        $role_id = Role::where('code', 'P')->first()->id;
        return view('livewire.sale.proforma.mant-component')->with(compact('person', 'role_id', 'arr_actions'));
    }

    /**
     * Función para regresar
     */
    public function back_button(){
        redirect()->route('proforma');
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
    public function updatedCurrencyId($value)
    {
        $currency = Currency::find($this->currency_id);
        if($currency){
            $this->currency = [
                "symbol" => $currency->symbol,
                "code" => $currency->code,
                "name" => $currency->name,
                "id" => $currency->id,
            ];
        }
    }

    public function updatedShoppingCar()
    {
        foreach ($this->shopping_car as $key => $value) {
            if(!$value["quantity"] || trim($value["quantity"]) == "" || trim($value["quantity"]) <= 0 ){
                $value["quantity"] = "1";
                $this->shopping_car[$key]["quantity"] = "1";
            }
            if(!$value["sell_price"] || trim($value["sell_price"]) == "" || trim($value["sell_price"]) < 0 ){
                $value["sell_price"] = "0.00";
                $this->shopping_car[$key]["sell_price"] = "0.00";
                $this->shopping_car[$key]["sell_price_min"] = "0.00";
            }
        }
        $this->calculate_total();
    }

    public function updatedDiscount()
    {
        $this->calculate_total();
    }

    //SUBTOTAL = SUMA DE PRODUCTOS; TOTAL = SUBTOTAL - DESCUENTO (MONTO GLOBAL)
    private function calculate_total()
    {
        $subtotal = 0;
        foreach ($this->shopping_car as $key => $value) {
            $subtotal = $subtotal + $value["quantity"] * $value["sell_price"];
        }
        if(!is_numeric($this->discount) || $this->discount < 0){
            $this->discount = 0;
        }
        $this->subtotal = $subtotal;
        $this->total = $subtotal - $this->discount;
    }

    private function validate_discount()
    {
        if($this->discount > $this->subtotal){
            $this->emit('alert-error', [
                'title' => 'Error!',
                'text' => 'El descuento no puede ser mayor al subtotal.',
            ]);
            return false;
        }
        return true;
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
                "document_number" => $person->getDocumentNumber()
            ];
            $this->search_person = $this->supplier["name_complete"];
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
            $focus_quantity_id = "quantity_".$this->list_search_product[$key]["id"];
            $this->emit('focus', [
                'id' => $focus_quantity_id
            ]);
            foreach ($this->shopping_car as $key2 => $value) {
                if($value["product_id"] == $this->list_search_product[$key]["id"]){
                    if($this->list_search_product[$key]["due_dates"]!="" && count($this->list_search_product[$key]["due_dates"])>0){
                        $due_date = $this->list_search_product[$key]["due_dates"][0]["due_date"];
                    }else{
                        $due_date = "";
                    }
                    $this->shopping_car[$key2] = [
                        "product_id" => $this->list_search_product[$key]["id"],
                        "product_name" => $this->list_search_product[$key]["name"],
                        "presentation" => $this->list_search_product[$key]["presentation"],
                        "unit_id" => $this->list_search_product[$key]["base_unit_id"],
                        "currency_id" => $this->shopping_car[$key2]["currency_id"],
                        "units" => $this->list_search_product[$key]["units"],
                        "due_dates" => $this->list_search_product[$key]["due_dates"],
                        "due_date" => $due_date,
                        "delivery_times" => $this->list_search_product[$key]["delivery_times"],
                        "delivery_time" => $this->list_search_product[$key]["delivery_times"],
                        "quantity" => $this->shopping_car[$key2]["quantity"] + 1,
                        "sell_price" => (!is_null($this->shopping_car[$key2]["sell_price"])?$this->shopping_car[$key2]["sell_price"]:$this->list_search_product[$key]["sell_price"][0]),
                        "sell_price_min" => (!is_null($this->shopping_car[$key2]["sell_price_min"])?$this->shopping_car[$key2]["sell_price_min"]:$this->list_search_product[$key]["sell_price_min"][0]),
                    ];
                    $this->updatedShoppingCar();
                    return true;
                }
            }
            if($this->list_search_product[$key]["due_dates"]!="" && count($this->list_search_product[$key]["due_dates"])>0){
                $due_date = $this->list_search_product[$key]["due_dates"][0]["due_date"];
            }else{
                $due_date = "";
            }
            if(count($this->list_search_product[$key]["sell_price"])==1){
                $price = $this->list_search_product[$key]["sell_price"][0];
                $price_min = $this->list_search_product[$key]["sell_price_min"][0];
            }else{
                foreach ($this->list_search_product[$key]["units"] as $k => $v) {
                    if($v["id"]==$this->list_search_product[$key]["base_unit_id"]){
                        $price = $v["sell_price"];
                        $price_min = $v["sell_price_min"];
                    }
                }
            }
            $this->shopping_car[] = [
                "product_id" => $this->list_search_product[$key]["id"],
                "product_name" => $this->list_search_product[$key]["name"],
                "presentation" => $this->list_search_product[$key]["presentation"],
                "unit_id" => $this->list_search_product[$key]["base_unit_id"],
                "currency_id" => $this->currency_id,
                "units" => $this->list_search_product[$key]["units"],
                "due_dates" => $this->list_search_product[$key]["due_dates"],
                "due_date" => $due_date,
                "delivery_time" => $this->list_search_product[$key]["delivery_times"],
                "delivery_times" => $this->list_search_product[$key]["delivery_times"],
                "quantity" => 1,
                "sell_price" => $price,
                "sell_price_min" => $price_min,
            ];
            $this->updatedShoppingCar();
        }
    }

    public function quit_car_item($key){
        unset($this->shopping_car[$key]);
        $this->updatedShoppingCar();
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
                        "presentation" => $product_search["presentation"],
                        "unit_id" => $product_search["base_unit_id"],
                        "currency_id" => $this->shopping_car[$key2]["currency_id"],
                        "units" => $product_search["units"],
                        "quantity" => $this->shopping_car[$key2]["quantity"] + 1,
                        "sell_price" => $this->shopping_car[$key2]["sell_price"],
                        "sell_price_min" => $this->shopping_car[$key2]["sell_price_min"],
                    ];
                    $this->updatedShoppingCar();
                    return true;
                }
            }
            $this->shopping_car[] = [
                "product_id" => $product_search["id"],
                "product_name" => $product_search["name"],
                "presentation" => $product_search["presentation"],
                "unit_id" => $product_search["base_unit_id"],
                "currency_id" => $this->currency_id,
                "units" => $product_search["units"],
                "quantity" => 1,
                "sell_price" => $product_search["sell_price"],
                "sell_price_min" => $product_search["sell_price_min"],
            ];
            $this->updatedShoppingCar();
        }
    }
    /**
     * Función para guardar
     */
    public function save($comment){
        $this->comment = $comment;
        // Validator::make(
        //     $this->inputs_validation(),
        //     $form_request->rules(),
        //     $form_request->messages(),
        //     $form_request->validationAttributes(),
        // )->validate();
        $this->calculate_total();
        if(!$this->validate_discount()){
            return true;
        }
        DB::beginTransaction();
        try {
            $document = Proforma::create($this->input_create_proforma());

            foreach ($this->shopping_car as $key => $item) {
                $detail = [
                    "due_date" => $item["due_date"],
                    "delivery_time" => $item["delivery_time"],
                ];
                $input_detail = [
                    'document_id' => $document->id,
                    'product_id' => $item["product_id"],
                    'unit_id' => $item["unit_id"],
                    'quantity' => $item["quantity"],
                    'currency_id' => $item["currency_id"],
                    'sell_price' => $item["sell_price"],
                    'status' => 'Y',
                    'detail' => json_encode($detail)
                ];
                DocumentDetail::create($input_detail);
            }

            DB::commit();

            Session::flash('success', 'Se registró correctamente');
            redirect()->route('proforma');


        } catch (\Exception $th) {

            DB::rollback();

            $this->emit('alert-error', [
                'title' => '¡Error!',
                'text' => 'Algo salio mal: ' . $th->getMessage(),
            ]);
        }
    }

    /**
     * Función para editar
     */
    public function update($comment){
        $this->comment = $comment;
        $this->calculate_total();
        if(!$this->validate_discount()){
            return true;
        }
        DB::beginTransaction();
        try {
            
            $this->proforma->update($this->input_edit_proforma());

            $this->proforma->details->map(function($detail){
                $detail->delete();
            });
            foreach ($this->shopping_car as $key => $item) {
                $detail = [
                    "due_date" => $item["due_date"],
                    "delivery_time" => $item["delivery_time"],
                ];
                $input_detail = [
                    'document_id' => $this->proforma->id,
                    'product_id' => $item["product_id"],
                    'unit_id' => $item["unit_id"],
                    'quantity' => $item["quantity"],
                    'sell_price' => $item["sell_price"],
                    'status' => 'Y',
                    'detail' => json_encode($detail)
                ];
                DocumentDetail::create($input_detail);
            }

            DB::commit();

            Session::flash('success', 'Se editó correctamente');
            redirect()->route('proforma');

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
            "client_id" => $this->supplier["id"],
            "number" => $this->number,
            "comment" => $this->comment,
            "shopping_car" => $this->shopping_car
        ];
    }

    private function input_create_proforma(){
        $detail = [
            "comment" => $this->comment ? $this->comment : "",
            "validez" => $this->validez ? $this->validez : "",
            "condicion" => $this->condicion ? $this->condicion : ""
        ];
        return [
            "type" => "C",
            'client_id' => $this->supplier["id"],
            'user_id' => Auth::user()->id,
            "currency_id" => $this->currency_id,
            'branch_office_id' => Auth::user()->branch_office_id,
            "date_time" => $this->date_time,
            "number" => $this->number,
            "total" => $this->total,
            "discount" => $this->discount,
            // "exchange_rate" => $this->exchange_rate,
            'detail' => json_encode($detail),
            'status' => 'P'
        ];
    }

    private function input_edit_proforma(){
        $detail = [
            "comment" => $this->comment ? $this->comment : "",
            "validez" => $this->validez ? $this->validez : "",
            "condicion" => $this->condicion ? $this->condicion : ""
        ];
        return [
            "type" => "C",
            'client_id' => $this->supplier["id"],
            'user_id' => Auth::user()->id,
            'branch_office_id' => Auth::user()->branch_office_id,
            'date_time' => $this->date_time,
            "total" => $this->total,
            "discount" => $this->discount,
            'detail' => json_encode($detail),
            'status' => $this->proforma->status,
        ];
    }


}
