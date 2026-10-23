<?php

namespace App\Http\Livewire\Sale\Sale;

use App\Http\LivewireRequest\Purchase\Purchase\PurchaseRequest;
use App\Http\Traits\Maintenance\PersonTrait;
use App\Http\Traits\Maintenance\ProductTrait;
use App\Http\Traits\Sale\ProformaTrait;
use App\Http\Traits\Sale\OrderTrait;
use App\Http\Traits\Sale\SaleTrait;
use App\Http\Traits\Sale\SaleGuideTrait;
use App\Http\Traits\Store\LotTrait;
use App\Models\Card;
use App\Models\Currency;
use App\Models\DocumentDetail;
use App\Models\DocumentPayment;
use App\Models\DocumentType;
use App\Models\ExchangeRate;
use App\Models\Order;
use App\Models\SaleGuide;
use App\Models\ReferralGuide;
use App\Models\Payment;
use App\Models\Person;
use App\Models\PersonProduct;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\Proforma;
use App\Models\PurchaseRequirement;
use App\Models\Role;
use App\Models\Sale;
use App\Models\Lot;
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
    use ProformaTrait;
    use OrderTrait;
    use SaleTrait;
    use LotTrait;
    use SaleGuideTrait;

    protected $aditional_actions;
    protected $arr_actions;

    public $sale;

    public $modal_document_detail = false;
    public $modal_supplier = false;

    public $document_type_id, $client_id, $document_id, $document2_id, $currency_id, $type, $declaration, $schedule_time, $number, $date_time, $total, $exchange_rate, $pending_amount, $comment,$order, $address_person;
    public $paided_amount;

    public $document_type_name;
    public $branch_office;
    public $supplier;
    public $personal;
    public $currency;
    public $document_types, $currencies;
    public $payment_methods;
    public $payments;
    public $document_payments;

    public $search_document;
    public $search_list_document;

    public $search_document_order;
    public $search_list_document_order;

    public $search_document_guide;
    public $search_list_document_guide;

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
    public $lot_id;
    public $detraccion;
    public $detracciones,$porcentaje;
    public $allowed_payment_types = ['C', 'D'];
    public $allowed_declaration_types = ['C', 'D'];
    //DESCUENTO: "T" = TOTAL (MONTO GLOBAL), "I" = POR ITEM (% POR LINEA); SOLO UNO A LA VEZ
    public $discount_type = "T";
    public $global_discount = 0;
    public $subtotal = 0;
    public $discount_amount = 0;

    protected $listeners = [
        'toggle-modal-supplier' => 'toggle_supplier',
    ];

    public function hydrate()
    {
        $this->resetValidation();
    }

    public function mount(Sale $sale)
    {
        $this->sale = $sale;
        $this->search_list_people = [];
        $this->search_list_personal = [];
        $this->search_list_lot = [];
        $this->search_list_serie = [];
        $this->search_list_document = [];
        $this->search_list_document_order = [];
        $this->search_list_document_guide = [];
        $this->list_search_product = [];
        $this->document_payments = [];
        $this->detracciones = array('' => '--Seleccione--',
                            '012' => '012 - INTERMEDIACION LABORAL Y TERCIARIZACION',
                            '019' => '019 - ARRENDAMIENTO DE BIENES',
                            '020' => '020 - MANTENIMIENTO Y REPARACION DE BIENES MUEBLES',
                            '021' => '021 - MOVIMIENTO DE CARGA',
                            '022' => '022 - OTROS SERVICIOS EMPRESARIALES',
                            '024' => '024 - COMISION MERCANTIL',
                            '025' => '025 - FABRICACION DE BIENES POR ENCARGO',
                            '026' => '026 - SERVICIO DE TRANSPORTE DE PERSONAS ',
                            '030' => '030 - CONTRATOS DE CONSTRUCCION',
                            '037' => '037 - DEMAS SERVICIOS GRAVADOS CON EL IGV' ,
                            );
        $this->detraccion = '';
        $this->porcentaje = array('' => '0',
                            '012' => '12',
                            '019' => '10',
                            '020' => '12',
                            '021' => '10',
                            '022' => '12',
                            '024' => '10',
                            '025' => '10',
                            '026' => '10',
                            '030' => '4',
                            '037' => '12' ,
                            );
        $this->document_types = Sale::list_document_types();
        $this->currencies = Currency::order_currency()->get();

        //POR DEFECTO FACTURA DE VENTA (13); SI NO EXISTE, EL PRIMER TIPO
        $default_document_type = $this->document_types->firstWhere('id', 13) ?? $this->document_types[0];
        $this->document_type_id = $default_document_type->id;
        $this->document_type_name = $default_document_type->name;
        $this->currency_id = $this->currencies[0]->id;
        $this->type = "D";
        $this->declaration = "C";
        $this->date_time = date('Y-m-d');
        $this->total = 0;
        $this->exchange_rate = "1";
        $this->pending_amount = 0;
        $this->comment = "";
        $this->number = $this->getNextNumberSale($this->document_type_id);

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

        $this->payment_methods[] = [
            "type" => "E",
            "name" => "EFECTIVO",
        ];

        $cards = Card::all();
        foreach ($cards as $key => $card) {
            $this->payment_methods[] = [
                "type" => "T",
                "name" => $card->name,
            ];
        }
        $transfers = TransferMethod::all();
        foreach ($transfers as $key => $transfer) {
            $this->payment_methods[] = [
                "type" => "D",
                "name" => $transfer->name,
            ];
        }

        $this->payments[] = [
            "type" => "E",
            "name" => "EFECTIVO",
            "amount" => "0.00",
            "date" => date("Y-m-d"),
            "comment" => "",
        ];


        if($sale->id != null){
            $this->document_type_id = $this->sale->document_type_id;
            $this->client_id = $this->sale->client_id;
            $this->document_id = $this->sale->document_id;
            $this->document_type_name = DocumentType::find($this->sale->document_id)->name;
            $this->currency_id = $this->sale->currency_id;
            $this->type = $this->sale->type;
            $this->schedule_time = $this->sale->schedule_time;
            $this->number = $this->sale->number;
            $this->date_time = date_format(new \DateTime($this->sale->date_time), 'Y-m-d');
            $this->total = $this->sale->total;
            $this->exchange_rate = $this->sale->exchange_rate;
            $this->pending_amount = $this->sale->pending_amount;
            $this->comment = $this->sale->comment();

            $this->currency = [
                "symbol" => $this->sale->currency->symbol,
                "code" => $this->sale->currency->code,
                "name" => $this->sale->currency->name,
                "id" => $this->sale->currency->id,
            ];

            $branch_office = $sale->branch_office;
            $supplier = $sale->client;
            $personal = $sale->person;

            $details = $sale->details;
            foreach ($details as $key => $value) {
                $product = Product::find($value["product_id"]);
                if($product){
                    $this->shopping_car[] = [
                        "product_id" => $value["product_id"],
                        "product_name" => $product->name.' - '.$product->presentation,
                        "product_name_original" => $product->name.' - '.$product->presentation,
                        "product_type" => $product->type,
                        "unit_id" => $value["unit_id"],
                        "currency_id" => $value["currency_id"],
                        "units" => $product->all_only_units,
                        "quantity" => sprintf('%g', $value["quantity"]),
                        "sell_price" => sprintf('%g', $value["sell_price"]),
                        "sell_price_min" => sprintf('%g', $value["sell_price"])
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
        $this->search_products();
    }

    public function render()
    {
        $this->load_actions();
        $arr_actions = $this->arr_actions;
        $this->sync_payment_types();

        $person = new Person();
        $role_id = Role::where('code', 'C')->first()->id;
        return view('livewire.sale.sale.mant-component')->with(compact('person', 'role_id', 'arr_actions'));
    }

    //FORMAS DE PAGO PERMITIDAS SEGUN EL CLIENTE SELECCIONADO (SIN CLIENTE = AMBAS)
    private function sync_payment_types(){
        $client = (isset($this->supplier["id"]) && $this->supplier["id"] != "") ? Person::find($this->supplier["id"]) : null;
        $this->allowed_payment_types = $client ? $client->getAllowedPaymentTypes() : ['C', 'D'];
        if(!in_array($this->type, $this->allowed_payment_types)){
            $this->type = $this->allowed_payment_types[0];
            $this->updatedType();
        }
        //DECLARACION PERMITIDA SEGUN EL CLIENTE (SOLO SE ELIGE EN VENTAS AL CREDITO)
        $this->allowed_declaration_types = $client ? $client->getAllowedDeclarationTypes() : ['C', 'D'];
        if($this->type == "D" && !in_array($this->declaration, $this->allowed_declaration_types)){
            $this->declaration = $this->allowed_declaration_types[0];
        }
    }

    /**
     * Función para regresar
     */
    public function back_button(){
        redirect()->route('sale');
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
            if($currency->base == "Y"){
                $this->exchange_rate = "1";
            }else{
                $exchange_rate = ExchangeRate::today($currency->id);
                $this->exchange_rate = $exchange_rate ? $exchange_rate->value : "0.00";
            }
        }
    }

    public function updatedShoppingCar()
    {
        $subtotal = 0;
        $total = 0;
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
            $percent = $this->item_discount_percent($key);
            $line = $value["quantity"] * $value["sell_price"];
            $subtotal = $subtotal + $line;
            $total = $total + $line * (1 - $percent / 100);
        }
        if($this->discount_type == "T"){
            if(!is_numeric($this->global_discount) || $this->global_discount < 0){
                $this->global_discount = 0;
            }
            $total = $subtotal - $this->global_discount;
        }
        $this->subtotal = $subtotal;
        $this->discount_amount = $subtotal - $total;
        $this->total = $total;
        $this->updatedType();
    }

    //% DE DESCUENTO DE LA LINEA (ENTERO DE 0 A 100); SOLO APLICA EN MODO POR ITEM
    //ENTERO PORQUE EL FACTURADOR ESCRIBE EL FACTOR EN EL XML CON 2 DECIMALES (5% = 0.05)
    private function item_discount_percent($key)
    {
        $percent = $this->shopping_car[$key]["discount_percent"] ?? 0;
        if(!is_numeric($percent) || $percent < 0 || $percent > 100 || $this->discount_type != "I"){
            $percent = 0;
        }
        $percent = (int) round($percent);
        $this->shopping_car[$key]["discount_percent"] = $percent;
        return $percent;
    }

    public function updatedDiscountType()
    {
        if($this->discount_type == "I"){
            $this->global_discount = 0;
        }else{
            foreach ($this->shopping_car as $key => $value) {
                $this->shopping_car[$key]["discount_percent"] = 0;
            }
        }
        $this->updatedShoppingCar();
    }

    public function updatedGlobalDiscount()
    {
        $this->updatedShoppingCar();
    }

    private function validate_discount(){
        if($this->discount_type == "T" && $this->global_discount > $this->subtotal){
            $this->emit('alert-error', [
                'title' => 'Error!',
                'text' => 'El descuento no puede ser mayor al subtotal.',
            ]);
            return false;
        }
        return true;
    }

    public function updatedType()
    {
        if($this->type == "D"){
            foreach ($this->payments as $key => $value) {
                if($value["name"] == "EFECTIVO"){
                    $this->payments[$key]["amount"] = "0.00";
                }
            }
        }
        $this->updatedPayments();

    }

    public function updatedPayments(){
        if($this->type == "C"){
            $total_no_cash = 0;
            foreach ($this->payments as $key => $value) {
                if(!$this->payments[$key]["amount"] || $this->payments[$key]["amount"] < "0"){
                    $this->payments[$key]["amount"] = "0.00";
                }
                if($value["name"] != "EFECTIVO"){
                    $total_no_cash = $total_no_cash + $this->payments[$key]["amount"];
                }
            }
            foreach ($this->payments as $key => $value) {
                if($value["name"] == "EFECTIVO"){
                    $this->payments[$key]["amount"] = $this->total - $total_no_cash;
                    if($this->payments[$key]["amount"] < 0){
                        $this->payments[$key]["amount"] = "0.00";
                    }
                }
            }
            $this->pending_amount = 0;
            $this->document_payments = [];
        }else{
            $paided = 0;
            foreach ($this->payments as $key => $value) {
                if(!$this->payments[$key]["amount"] || $this->payments[$key]["amount"] < "0"){
                    $this->payments[$key]["amount"] = "0.00";
                }
                $paided = $paided + $this->payments[$key]["amount"];
            }
            $this->pending_amount = $this->total - $paided;
            if($this->pending_amount < 0){
                $this->pending_amount == 0;
            }
            $this->calculate_payments();
            if(count($this->document_payments) == 0){
                if($this->pending_amount > 0){
                    $this->add_document_payment();
                }
            }else{
                $this->calculate_installments();
            }
        }
    }

    public function updatingDocumentTypeId($value)
    {
        $document_type = DocumentType::find($value);
        if($document_type){
            $this->document_type_name = $document_type->name;
            $this->number = $this->getNextNumberSale($document_type->id, $this->detraccion != '');
        }
    }

    public function updatedDetraccion($value)
    {
        $this->number = $this->getNextNumberSale($this->document_type_id, $value != '');
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
            $this->address_person = $person->address;
            if(!is_null($person->supervisor_id) && trim($person->supervisor_id)!="" && $person->supervisor_id!="0"){
                $this->select_active_personal($person->supervisor_id);
            }
        }
    }

    /**
     * Funciones para filtro de Vendedor
     */
    public function updatingSearchPersonal($value)
    {
        if($value != ""){
            $this->search_list_personal = $this->search_autocomplete2($value,['A','S']);
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
        //if($value != ""){
            $this->search_list_lot = $this->search_autocompleteLot($value, $this->shopping_car[$this->document_detail_key]["product_id"],"Y");
        //}else{
        //    $this->search_list_lot = [];
        //}
    }

    public function select_active_lot($lot_id){
        $lot = Lot::find($lot_id);
        if($lot){
            $this->product_due_date = $lot->due_date;
            $this->lot_quantity = $lot->lot_quantity;
            $this->product_lot = $lot->lot;
            $this->product_barcode = $lot->barcode;
            $this->lot_remain = sprintf('%g', $lot->remain);
            $this->lot_id = $lot->id;
        }
    }

    /**
     * Funciones para filtro de Requerimiento
     */
    public function updatingSearchDocument($value)
    {
        if($value != ""){
            $this->search_list_document = $this->search_proforma_autocomplete($value);
        }else{
            $this->search_list_document = [];
        }
    }

    public function select_active_document($document_id){
        $document = Proforma::find($document_id);
        if($document){
            $this->document2_id = $document->id;
            $this->search_document = $document->number;
            $this->shopping_car = [];
            $details = $document->details;
            foreach ($details as $key => $value) {
                $product = Product::find($value["product_id"]);
                if($product){
                    $line_detail = ($value->detail && trim($value->detail) != "") ? json_decode($value->detail, true) : [];
                    $this->shopping_car[] = [
                        "product_id" => $value["product_id"],
                        "product_name" => $product->name.' - '.$product->presentation,
                        "product_type" => $product->type,
                        "product_name_original" => $product->name.' - '.$product->presentation,
                        "unit_id" => $value["unit_id"],
                        "currency_id" => $value["currency_id"],
                        "units" => $product->all_only_units,
                        "quantity" => sprintf('%g', $value["quantity"]),
                        "sell_price" => $value["sell_price"],
                        "sell_price_min" => $value["sell_price"],
                        "product_moves" => [],
                        "declarar" => "S",
                        "discount_percent" => (array_key_exists('descuento', $line_detail)) ? (int) $line_detail["descuento"] : 0,
                    ];
                }
            }
            //FORMA DE PAGO DE LA PROFORMA (SE RECALCULAN LOS PAGOS EN updatedShoppingCar)
            if(in_array($document->type, ['C', 'D'])){
                $this->type = $document->type;
            }
            //DESCUENTO DE LA PROFORMA: POR ITEM => % EN CADA LINEA; TOTAL => DESCUENTO TOTAL DE LA VENTA
            $proforma_detail = ($document->detail && trim($document->detail) != "") ? json_decode($document->detail, true) : [];
            if(array_key_exists('discount_type', $proforma_detail) && $proforma_detail["discount_type"] == "I"){
                $this->discount_type = "I";
                $this->global_discount = 0;
            }else{
                $this->discount_type = "T";
                $this->global_discount = $document->discount ? (float) $document->discount : 0;
            }
            if($document->client){
                $this->supplier = [
                    'id' => ($document->client) ? $document->client->id : "",
                    'type' => ($document->client) ? $document->client->type : "6",
                    'name_complete' => ($document->client) ? $document->client->getNameComplete() : "",
                    'document_number' => ($document->client) ? $document->client->getDocumentNumber() : "",
                ];
                $this->search_person = $this->supplier["name_complete"];
                $this->address_person = $document->client->address;
                if(!is_null($document->client->supervisor_id) && trim($document->client->supervisor_id)!="" && $document->client->supervisor_id!="0"){
                    $this->select_active_personal($document->client->supervisor_id);
                }
            }
            $this->updatedShoppingCar();
        }
    }

    public function quit_document(){
        $this->document2_id = null;
        $this->search_document = "";
        $this->shopping_car = [];
        $this->global_discount = 0;
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
     * Funciones para filtro de Requerimiento
     */
    public function updatingSearchDocumentOrder($value)
    {
        if($value != ""){
            $this->search_list_document_order = $this->search_order_autocomplete($value);
        }else{
            $this->search_list_document_order = [];
        }
    }

    public function select_active_document_order($document_id){
        $document = Order::find($document_id);
        if($document){
            $this->document_id = $document->id;
            $this->search_document_order = $document->number;
            //FORMA DE PAGO DEL PEDIDO (SE RECALCULAN LOS PAGOS EN updatedShoppingCar)
            if(in_array($document->type, ['C', 'D'])){
                $this->type = $document->type;
            }
            $this->shopping_car = [];
            $details = $document->details;
            foreach ($details as $key => $value) {
                $product = Product::find($value["product_id"]);
                $prices = $this->search_products_fast(null, '', null, '',$product->id);
                if($product){
                    if(count($prices[0]["sell_price"])==1){
                        $price = $prices[0]["sell_price"][0];
                        $price_min = $prices[0]["sell_price_min"][0];
                    }else{
                        foreach ($prices[0]["units"] as $k => $v) {
                            if($v["id"]==$prices[0]["base_unit_id"]){
                                $price = $v["sell_price"];
                                $price_min = $v["sell_price_min"];
                            }
                        }
                    }
                    $price = $value->sell_price;
                    $this->shopping_car[] = [
                        "product_id" => $value["product_id"],
                        "product_name" => $product->name.' - '.$product->presentation,
                        "product_type" => $product->type,
                        "product_name_original" => $product->name.' - '.$product->presentation,
                        "unit_id" => $value["unit_id"],
                        "currency_id" => $value["currency_id"],
                        "units" => $product->all_only_units,
                        "quantity" => sprintf('%g', $value["quantity"]),
                        "sell_price" => $price,
                        "sell_price_min" => $price_min,
                        "product_moves" => [],
                        "declarar" => "S",
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
                $this->address_person = $document->client->address;
            }
            $this->updatedShoppingCar();
        }
    }

    public function quit_document_order(){
        $this->document_id = null;
        $this->search_document_order = "";
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
     * Funciones para filtro de Guia
     */
    public function updatingSearchDocumentGuide($value)
    {
        if($value != ""){
            $this->search_list_document_guide = $this->search_guide_autocomplete($value);
        }else{
            $this->search_list_document_order = [];
        }
    }

    public function select_active_document_guide($document_id){
        $document = SaleGuide::find($document_id);
        if(is_null($document)){
            $document = ReferralGuide::find($document_id);
        }
        if($document){
            $this->document_id = $document->id;
            $this->search_document_guide = $document->number;
            $this->shopping_car = [];
            $details = $document->details;
            foreach ($details as $key => $value) {
                $product = Product::find($value["product_id"]);
                $prices = $this->search_products_fast(null, '', null, '',$product->id);
                if($product){
                    $product_moves = [];
                    $product_moves_sale = DocumentDetail::product_moves_guide($value->id,$document->id);//para los detalles de la venta
                    foreach ($product_moves_sale as $product_move) {
                        if($product->type=="M"){
                            $product_moves[] = [
                                "id" => "",
                                "product_id" => $product->id,
                                "product_name" => $product->name.' - '.$product->presentation,
                                "product_type" => $product->type,
                                "product_serie_id" => $product_move->product_serie_id,
                                "product_serie" => $product_move->product_serie->serie,
                                "lot_id" => 0,
                                "lot_barcode" => "",
                                "lot_name" => "",
                                "lot_due_date" => "",
                                "quantity" => $product_move->quantity,
                            ];
                        }else{
                            $product_moves[] = [
                                "id" => "",
                                "product_id" => $product->id,
                                "product_name" => $product->name.' - '.$product->presentation,
                                "product_type" => $product->type,
                                "product_serie_id" => "",
                                "product_serie" => "",
                                "lot_id" => $product_move->lot->id,
                                "lot_barcode" => $product_move->lot->barcode,
                                "lot_name" => $product_move->lot->lot,
                                "lot_due_date" => $product_move->lot->due_date,
                                "quantity" => $product_move->quantity,
                            ];
                        }
                    }
                    if(count($prices[0]["sell_price"])==1){
                        $price = $prices[0]["sell_price"][0];
                        $price_min = $prices[0]["sell_price_min"][0];
                    }else{
                        foreach ($prices[0]["units"] as $k => $v) {
                            if($v["id"]==$prices[0]["base_unit_id"]){
                                $price = $v["sell_price"];
                                $price_min = $v["sell_price_min"];
                            }
                        }
                    }
                    $this->shopping_car[] = [
                        "product_id" => $value["product_id"],
                        "product_name" => $product->name.' - '.$product->presentation,
                        "product_type" => $product->type,
                        "product_name_original" => $product->name.' - '.$product->presentation,
                        "unit_id" => $value["unit_id"],
                        "currency_id" => $value["currency_id"],
                        "units" => $product->all_only_units,
                        "quantity" => sprintf('%g', $value["quantity"]),
                        "sell_price" => $price,
                        "sell_price_min" => $price_min,
                        "product_moves" => $product_moves,
                        "declarar" => "S",
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
                $this->address_person = $document->client->address;
            }
            $this->updatedShoppingCar();
        }
    }

    public function quit_document_guide(){
        $this->document_id = null;
        $this->search_document_guide = "";
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
                    $this->shopping_car[$key2] = [
                        "product_id" => $this->list_search_product[$key]["id"],
                        "product_name" => $this->list_search_product[$key]["name"],
                        "product_type" => $this->list_search_product[$key]["type"],
                        "product_name_original" => $this->list_search_product[$key]["name"].' '.$this->list_search_product[$key]["model"].' '.$this->list_search_product[$key]["presentation"].' '.$this->list_search_product[$key]["brand_name"],
                        "unit_id" => $this->list_search_product[$key]["base_unit_id"],
                        "currency_id" => $this->shopping_car[$key2]["currency_id"],
                        "units" => $this->list_search_product[$key]["units"],
                        "quantity" => $this->shopping_car[$key2]["quantity"] + 1,
                        "sell_price" => $this->shopping_car[$key2]["sell_price"],
                        "product_moves" => $this->shopping_car[$key2]["product_moves"],
                        "declarar" => $this->shopping_car[$key2]["declarar"],
                        "sell_price_min" => $this->list_search_product[$key]["sell_price_min"],
                    ];
                    $this->updatedShoppingCar();
                    return true;
                }
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
            if($this->supplier["id"]!=""){
                $dataPrice = PersonProduct::where('person_id','=',$this->supplier["id"])->get();
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
                "product_name_original" => $this->list_search_product[$key]["name"].' '.$this->list_search_product[$key]["model"].' '.$this->list_search_product[$key]["presentation"].' '.$this->list_search_product[$key]["brand_name"],
                "unit_id" => $this->list_search_product[$key]["base_unit_id"],
                "currency_id" => $this->currency_id,
                "units" => $this->list_search_product[$key]["units"],
                "quantity" => 1,
                "sell_price" => $price,
                "sell_price_min" => $price_min,
                "product_moves" => [],
                "declarar" => "S",
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
                $this->shopping_car[$key]["sell_price_min"] = $v["sell_price_min"];
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
                        "product_name_original" => $product_search["name"],
                        "product_name" => $product_search["name"].' - '.$product_search["presentation"],
                        "product_type" => $product_search["type"],
                        "unit_id" => $product_search["base_unit_id"],
                        "currency_id" => $this->shopping_car[$key2]["currency_id"],
                        "units" => $product_search["units"],
                        "quantity" => $this->shopping_car[$key2]["quantity"] + 1,
                        "sell_price" => $this->shopping_car[$key2]["sell_price"],
                        "sell_price_min" => $this->shopping_car[$key2]["sell_price_min"],
                        "declarar" => $this->shopping_car[$key2]["declarar"],
                    ];
                    $this->updatedShoppingCar();
                    return true;
                }
            }
            $this->shopping_car[] = [
                "product_id" => $product_search["id"],
                "product_name_original" => $product_search["name"],
                "product_name" => $product_search["name"].' - '.$product_search["presentation"],
                "product_type" => $product_search["type"],
                "unit_id" => $product_search["base_unit_id"],
                "currency_id" => $this->currency_id,
                "units" => $product_search["units"],
                "quantity" => 1,
                "sell_price" => "0.00",
                "sell_price_min" => "0.00",
                "declarar" => "S",
            ];
            $this->updatedShoppingCar();
        }
    }

    /**
     * Funciones para pagos
     */
    public function select_payment_method($key){
        if(array_key_exists($key, $this->payment_methods)){
            foreach ($this->payments as $value) {
                if($value["name"] == $this->payment_methods[$key]["name"]){
                    $this->emit('alert-warning', [
                        'title' => 'Error!',
                        'text' => 'Ya seleccionó esa forma de pago.',
                    ]);
                    return true;
                }
            }
            $this->payments[] = [
                "type" => $this->payment_methods[$key]["type"],
                "name" => $this->payment_methods[$key]["name"],
                "amount" => "0.00",
            ];
            $this->updatedPayments();
        }
    }

    public function quit_method_item($key){
        if(array_key_exists($key, $this->payments)){
            if($this->payments[$key]["name"] == "EFECTIVO"){
                $this->payments[$key]["amount"] = "0.00";
            }else{
                unset($this->payments[$key]);
            }
            $this->updatedPayments();
        }
    }

    protected function calculate_payments(){
        $cash_amount = 0;
        $card_amount = 0;
        $transfer_amount = 0;
        foreach ($this->payments as $key => $value) {
            if($value["type"] == "E"){
                $cash_amount = $cash_amount + $value["amount"];
            }
            if($value["type"] == "T"){
                $card_amount = $card_amount + $value["amount"];
            }
            if($value["type"] == "D"){
                $transfer_amount = $transfer_amount + $value["amount"];
            }
        }
        $status = "P";
        $this->paided_amount = $cash_amount + $card_amount + $transfer_amount;
        if($this->total == $this->paided_amount){
            $status = "C";
        }
        return [
            "cash_amount" => $cash_amount,
            "card_amount" => $card_amount,
            "transfer_amount" => $transfer_amount,
            "status" => $status,
        ];
    }

    public function add_document_payment(){

        $this->document_payments[] = [
            'payment_name' => "",
            'schedule_date' => "",
            'total_amount' => "0.00",
            'status' => 'P'
        ];
        $this->calculate_installments();
    }

    public function calculate_installments(){
        $last_date = $this->date_time;
        $last_str_date = strtotime($last_date);
        $installments = count($this->document_payments);
        if($installments > 0){
            $paid_per_installments = $this->pending_amount / $installments;
            $i = 1;
            foreach ($this->document_payments as $key => $value) {
                $this->document_payments[$key]['payment_name'] = 'CUOTA ' . str_pad($i, 3, '0', STR_PAD_LEFT);
                $this->document_payments[$key]['schedule_date'] = date('Y-m-d', strtotime('+1 month', $last_str_date));
                $this->document_payments[$key]['total_amount'] = $paid_per_installments;
                $last_date = $this->document_payments[$key]['schedule_date'];
                $last_str_date = strtotime($last_date);
                $i++;
            }

        }
    }

    public function quit_installments($key){
        if(array_key_exists($key, $this->document_payments)){
            unset($this->document_payments[$key]);
            $this->calculate_installments();
        }
    }

    /**
     * Función que valida los pagos
     */
    private function validate_payments(){
        $total = 0;
        foreach ($this->payments as $key => $value) {
            $total = $total + $value["amount"];
        }
        if($this->type == "C" && $this->total != $total){
            $this->emit('alert-error', [
                'title' => 'Error!',
                'text' => 'El monto pagado es diferente al total.',
            ]);
            return false;
        }
        if($this->total < $total){
            $this->emit('alert-error', [
                'title' => 'Error!',
                'text' => 'El monto pagado es mayor al total.',
            ]);
            return false;
        }
        if($this->type == "D"){
            $total_installments = $this->paided_amount;
            foreach ($this->document_payments as $key => $value) {
                $total_installments += $value["total_amount"];
            }
            if($total_installments != $this->total){
                $this->emit('alert-error', [
                    'title' => 'Error!',
                    'text' => 'La sumatoria de las cuotas es diferente al total.',
                ]);
                return false;
            }
        }
        if($this->type=="C"){//CONTADO
            $this->declaration = "C";//declaracion al contado
        }
        return true;
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
        $client = (isset($this->supplier["id"]) && $this->supplier["id"] != "") ? Person::find($this->supplier["id"]) : null;
        if($client && !in_array($this->type, $client->getAllowedPaymentTypes())){
            $this->emit('alert-error', [
                'title' => 'Error!',
                'text' => 'La forma de pago no está permitida para este cliente.',
            ]);
            return true;
        }
        if($client && $this->type == "D" && !in_array($this->declaration, $client->getAllowedDeclarationTypes())){
            $this->emit('alert-error', [
                'title' => 'Error!',
                'text' => 'La declaración no está permitida para este cliente.',
            ]);
            return true;
        }
        if(!$this->validate_discount()){
            return true;
        }
        if(!$this->validate_product_moves()){
            return true;
        }

        if(!$this->validate_payments()){
            return true;
        }
        DB::beginTransaction();
        try {

            $document_input = array_merge($this->input_create_purchase(), $this->calculate_payments());

            $document = Sale::create($document_input);

            $requirement = Proforma::find($this->document2_id);
            if($requirement){
                $requirement->update([
                    "document_id" => $document->id,
                    "status" => 'Y'
                ]);
            }

            $saleguide = SaleGuide::find($this->document_id);
            if(!is_null($saleguide)){
                $saleguide->update([
                    "document_id" => $document->id,
                    "status" => 'Y'
                ]);

                $document->update([
                    "dispatched" => 'Y'
                ]);
            }else{
                $saleguide = ReferralGuide::find($this->document_id);
                if(!is_null($saleguide)){
                    $saleguide->update([
                        "document_id" => $document->id,
                        "status" => 'Y'
                    ]);

                    $document->update([
                        "dispatched" => 'Y'
                    ]);
                }
            }

            foreach ($this->shopping_car as $key => $item) {
                $input_detail = [
                    'document_id' => $document->id,
                    'product_id' => $item["product_id"],
                    'unit_id' => $item["unit_id"],
                    'currency_id' => $this->currency_id,
                    'quantity' => $item["quantity"],
                    'sell_price' => $item["sell_price"],
                    'subtotal' => $item["quantity"]*$item["sell_price"]*(1 - ($item["discount_percent"] ?? 0)/100),
                    'dispatched_quantity' => 0,
                    'status' => 'N',
                    'detail' => json_encode(["producto"=>$item["product_name_original"],"declarar"=>$item["declarar"],"descuento"=>$item["discount_percent"] ?? 0]),
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
                            'document_detail_id' => $guide_detail->id,
                            'branch_office_id' => $document->branch_office_id,
                            'product_id' => $product_move["product_id"],
                            'product_serie_id' => $product_serie->id,
                            'quantity' => $product_move["quantity"],
                            'type' => $type,
                            'status' => "P",
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
            $payment_name = "PAGO ÚNICO";
            if($document->type == "D"){
                $payment_name = "PAGO INICIAL";
            }

            // $total_amount = $document->cash_amount + $document->card_amount + $document->transfer_amount;
            if($this->paided_amount > 0){
                $input_document_payment = [
                    'document_id' => $document->id,
                    'branch_office_id' => $document->branch_office_id,
                    'currency_id' => $document->currency_id,
                    'name' => $payment_name,
                    'date_time' => $document->date_time,
                    'total_amount' => $this->paided_amount,
                    'cash_amount' => $document->cash_amount,
                    'card_amount' => $document->card_amount,
                    'transfer_amount' => $document->transfer_amount,
                    'status' => 'Y'
                ];
                $docuemnt_payment = DocumentPayment::create($input_document_payment);
                foreach ($this->payments as $key => $value) {
                    if(($value["amount"] + 0 )>0){
                        if(!isset($value["date"])){
                            $value["date"] = date("Y-m-d");
                        }
                        if(!isset($value["comment"])){
                            $value["comment"] = "";
                        }
                        $input_payment = [
                            'document_id' => $document->id,
                            'document_payment_id' => $docuemnt_payment->id,
                            'currency_id' => $document->currency_id,
                            'date_time' => $value["date"],
                            'type' => $value["type"],
                            'method_name' => $value["name"],
                            'amount' => $value["amount"],
                            'operation_number' => $value["comment"],
                        ];
                        Payment::create($input_payment);
                    }
                }
            }


            if($document->type == "D"){
                foreach ($this->document_payments as $key => $value) {
                    if(($value["total_amount"] + 0) > 0){
                        $input_document_payment = [
                            'document_id' => $document->id,
                            'branch_office_id' => $document->branch_office_id,
                            'currency_id' => $document->currency_id,
                            'name' => $value["payment_name"],
                            'date_time' => $document->date_time,
                            'schedule_date' => $value["schedule_date"],
                            'total_amount' => $value["total_amount"],
                            'cash_amount' => 0.00,
                            'card_amount' => 0.00,
                            'transfer_amount' => 0.00,
                            'status' => 'P'
                        ];
                        DocumentPayment::create($input_document_payment);
                    }
                }
            }
            $bandService=true;
            foreach ($item["product_moves"] as $key => $product_move) {
                if($product_move["product_type"] != "S"){
                    $bandService=false;
                }
            }
            if($bandService){//SI SOLO ES SERVICIO
                $document->dispatched = 'Y';
                $document->save();
            }

            DB::commit();

            Session::flash('success', 'Se registró correctamente');
            Session::flash('id_venta', $document->id);
            Session::flash('tipodocumento', ($document->document_type_id==13?"Factura":"Boleta"));
            redirect()->route('sale');


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
            "schedule_time" => $this->schedule_time,
            "date_time" => $this->date_time,
            "number" => $this->number,
            "total" => $this->total,
            "exchange_rate" => $this->exchange_rate,
            "pending_amount" => $this->pending_amount,
            "comment" => $this->comment,
            "shopping_car" => $this->shopping_car,
            "payments" => $this->payments,
            "document_payments" => $this->document_payments
        ];
    }

    private function input_create_purchase(){
        $porcentaje = array('' => '0',
                            '012' => '12',
                            '019' => '10',
                            '020' => '12',
                            '021' => '10',
                            '022' => '12',
                            '024' => '10',
                            '025' => '10',
                            '026' => '10',
                            '030' => '4',
                            '037' => '12' ,
                            );
        $detail = [
            "comment" => $this->comment ? $this->comment : "",
            "orden" => $this->order,
            "declaration" => $this->declaration,
            "document2_id" => $this->document2_id,
            "detraccion" => $this->detraccion,
            "porcentajedetraccion" => $porcentaje[$this->detraccion],
            "cuentabn" => "00161567493",
            "discount_type" => $this->discount_type
        ];
        $person = Person::find($this->personal["id"]);
        return [
            "discount" => $this->discount_amount,
            "global_discount" => ($this->discount_type == "T") ? $this->global_discount : 0,
            "type" => $this->type,
            "document_type_id" => $this->document_type_id,
            "document_id" => $this->document_id,
            'client_id' => $this->supplier["id"],
            'person_id' => $this->personal["id"],
            'supervisor_id' => $person->supervisor_id,
            'user_id' => Auth::user()->id,
            "currency_id" => $this->currency_id,
            'branch_office_id' => Auth::user()->branch_office_id,
            "schedule_time" => $this->schedule_time,
            "date_time" => $this->date_time." ".date("H:i:s"),
            "number" => $this->getNextNumberSale($this->document_type_id, $this->detraccion != ''),
            "total" => $this->total,
            "exchange_rate" => $this->exchange_rate,
            "pending_amount" => $this->pending_amount,
            'detail' => json_encode($detail),
            'dispatched' => 'N'
        ];
    }

    /**
     * FUNCIONES PARA ASIGNAR SERIES O LOTES
     */
    public function add_product_move($key){
        $this->modal_document_detail = true;
        $this->document_detail_key = "" . $key;
        $this->clear_product_moves_data();
        if($this->shopping_car[$key]["product_type"]!="M"){
            $this->search_product_lot();
            $this->updatingSearchLot("");
        }else{
            $this->updatingSearchSerie("");
        }
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
                if($this->product_lot == $value["lot_name"] && $value["lot_id"] == $this->lot_id){
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
                ->where('remain','>',0)
                ->where('id',$this->lot_id)
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
        $this->lot_id = 0;
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
                ->where('status','like','Y')
                ->orderBy('due_date','asc')
                ->first();

        if($lot){
            $this->product_due_date = $lot->due_date;
            $this->lot_quantity = $lot->lot_quantity;
            $this->product_lot = $lot->lot;
            $this->product_barcode = $lot->barcode;
            $this->lot_remain = sprintf('%g', $lot->remain);
            $this->lot_id = $lot->id;
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
            if($this->shopping_car[$key]["product_type"]=="S"){//PARA SERVICIOS
                $total = $total + 1;
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
