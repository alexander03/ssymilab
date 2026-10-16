<?php

namespace App\Http\Livewire\Sale\Order;

use App\Http\Traits\General\StockTrait;
use App\Http\Traits\Maintenance\PersonTrait;
use App\Http\Traits\Maintenance\ProductTrait;
use App\Http\Traits\Sale\OrderTrait;
use App\Models\Currency;
use App\Models\DocumentDetail;
use App\Models\Order;
use App\Models\Person;
use App\Models\Product;
use App\Models\Role;
use App\Models\User;
use App\Models\Notification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Livewire\Component;

class MantComponent extends Component
{
    use OrderTrait;
    use PersonTrait;
    use ProductTrait;
    use StockTrait;

    protected $aditional_actions;
    protected $arr_actions;

    public $order;

    public $modal_supplier = false;

    public $client_id, $currency_id, $number, $date_time, $total, $comment;

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

    protected $listeners = [
        'toggle-modal-supplier' => 'toggle_supplier',
    ];

    public function mount(Order $order)
    {
        $this->order = $order;
        $this->search_list_people = [];
        $this->currencies = Currency::order_currency()->get();

        $this->currency_id = $this->currencies[0]->id;

        $this->currency = [
            "symbol" => $this->currencies[0]->symbol,
            "code" => $this->currencies[0]->code,
            "name" => $this->currencies[0]->name,
            "id" => $this->currencies[0]->id,
        ];

        if($order->id != null){
            $details = ($this->order->detail && trim($this->order->detail) != "") ? json_decode($this->order->detail, true) : [];
            $this->client_id = $this->order->client_id;
            $this->currency_id = $this->order->currency_id;
            $this->total = $this->order->total;
            $this->comment = (array_key_exists('comment', $details)) ? $details["comment"] : "";
            $this->number = $this->order->number;
            $this->date_time = date_format(new \DateTime($this->order->date_time), 'Y-m-d');

            $branch_office = $order->branch_office;
            $supplier = $order->client;
            $details = $order->details;

            $this->currency = [
                "symbol" => $this->order->currency->symbol,
                "code" => $this->order->currency->code,
                "name" => $this->order->currency->name,
                "id" => $this->order->currency->id,
            ];

            foreach ($details as $key => $value) {
                $product = Product::find($value["product_id"]);
                if($product){
                    $this->shopping_car[] = [
                        "product_id" => $value["product_id"],
                        "product_code" => $product->code,
                        "product_name" => $product->name.' - '.$product->presentation,
                        "product_name_original" => $product->name.' - '.$product->presentation,
                        "unit_id" => $value["unit_id"],
                        "currency_id" => $value["currency_id"],
                        "units" => $product->all_only_units,
                        "quantity" => sprintf('%g', $value["quantity"]),
                        "sell_price" => sprintf('%g', $value["sell_price"])
                    ];
                }
            }

        }else{
            $this->total = 0;
            $this->date_time = date('Y-m-d');
            $this->number = $this->getNextNumberOrder();

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

    }

    public function render()
    {
        $this->load_actions();
        $arr_actions = $this->arr_actions;

        $person = new Person();
        $role_id = Role::where('code', 'P')->first()->id;
        $this->search_products();
        return view('livewire.sale.order.mant-component')->with(compact('person', 'role_id', 'arr_actions'));
    }

    /**
     * Función para regresar
     */
    public function back_button(){
        redirect()->route('order');
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
        if($this->search_product != ""){
            $this->list_search_product = $this->search_products_fast(null, $this->search_product);
        }else{
            $this->list_search_product = [];
        }
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
                        "product_code" => $this->list_search_product[$key]["code"],
                        "product_name" => $this->list_search_product[$key]["name"],
                        "product_name_original" => $this->list_search_product[$key]["name"].' '.$this->list_search_product[$key]["model"].'  '.$this->list_search_product[$key]["presentation"],
                        "unit_id" => $this->list_search_product[$key]["base_unit_id"],
                        "currency_id" => $this->shopping_car[$key2]["currency_id"],
                        "units" => $this->list_search_product[$key]["units"],
                        "quantity" => $this->shopping_car[$key2]["quantity"] + 1,
                        "sell_price" => $this->shopping_car[$key2]["sell_price"],
                    ];
                    $this->updatedShoppingCar();
                    return true;
                }
            }
            $this->shopping_car[] = [
                "product_id" => $this->list_search_product[$key]["id"],
                "product_code" => $this->list_search_product[$key]["code"],
                "product_name" => $this->list_search_product[$key]["name"],
                "product_name_original" => $this->list_search_product[$key]["name"].' '.$this->list_search_product[$key]["model"].'  '.$this->list_search_product[$key]["presentation"],
                "unit_id" => $this->list_search_product[$key]["base_unit_id"],
                "currency_id" => $this->currency_id,
                "units" => $this->list_search_product[$key]["units"],
                "quantity" => 1,
                "sell_price" => $this->list_search_product[$key]["sell_price"][0],
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
                "product_name" => $product_search["name"],
                "unit_id" => $product_search["base_unit_id"],
                "currency_id" => $this->currency_id,
                "units" => $product_search["units"],
                "quantity" => 1,
                "sell_price" => $product_search["sell_price"],
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
        DB::beginTransaction();
        try {
            $document = Order::create($this->input_create_order());

            foreach ($this->shopping_car as $key => $item) {
                $input_detail = [
                    'document_id' => $document->id,
                    'product_id' => $item["product_id"],
                    'unit_id' => $item["unit_id"],
                    'quantity' => $item["quantity"],
                    'currency_id' => $item["currency_id"],
                    'sell_price' => $item["sell_price"],
                    'dispatched_quantity' => 0,
                    'status' => 'N'
                ];
                $detail = DocumentDetail::create($input_detail);

               //$this->decrease_available($document, $detail);
            }

            $users = User::all();

            foreach ($users as $user) {
                Notification::create([
                    'user_id' => $user->id,
                    'status' => 'N', //N New, S: Seen, A:Annulated
                    'message' => 'Nuevo pedido creado Nro: ' . $document->number,
                    'date_time_created' => date('Y-m-d H:i:s'),
                    'register_id' => $document->id,
                    'register_type' => 'ORDER'
                ]);
            }


            DB::commit();

            Session::flash('success', 'Se registró correctamente');
            redirect()->route('order');


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
        DB::beginTransaction();
        try {

            $this->order->update($this->input_edit_order());

            $this->order->details->map(function($detail){
                $detail->delete();
            });
            foreach ($this->shopping_car as $key => $item) {
                $input_detail = [
                    'document_id' => $this->order->id,
                    'product_id' => $item["product_id"],
                    'unit_id' => $item["unit_id"],
                    'quantity' => $item["quantity"],
                    'currency_id' => $item["currency_id"],
                    'sell_price' => $item["sell_price"],
                    'dispatched_quantity' => 0,
                    'status' => 'N'
                ];
                DocumentDetail::create($input_detail);
            }

            DB::commit();

            Session::flash('success', 'Se editó correctamente');
            redirect()->route('order');

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

    private function input_create_order(){
        $detail = [
            "comment" => $this->comment ? $this->comment : ""
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
            // "exchange_rate" => $this->exchange_rate,
            'detail' => json_encode($detail),
            'status' => 'P'
        ];
    }

    private function input_edit_order(){
        $detail = [
            "comment" => $this->comment ? $this->comment : ""
        ];
        return [
            'client_id' => $this->supplier["id"],
            'user_id' => Auth::user()->id,
            'branch_office_id' => Auth::user()->branch_office_id,
            'date_time' => $this->date_time,
            "total" => $this->total,
            'detail' => json_encode($detail),
            'status' => $this->order->status,
        ];
    }


}
