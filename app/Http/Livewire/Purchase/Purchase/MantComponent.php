<?php

namespace App\Http\Livewire\Purchase\Purchase;

use App\Http\LivewireRequest\Purchase\Purchase\PurchaseRequest;
use App\Http\Traits\Maintenance\PersonTrait;
use App\Http\Traits\Maintenance\ProductTrait;
use App\Http\Traits\Purchase\RequirementTrait;
use App\Models\Card;
use App\Models\Currency;
use App\Models\DocumentDetail;
use App\Models\DocumentPayment;
use App\Models\DocumentType;
use App\Models\ExchangeRate;
use App\Models\Payment;
use App\Models\Person;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\PurchaseRequirement;
use App\Models\Role;
use App\Models\TransferMethod;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Livewire\Component;
use Livewire\WithFileUploads;

class MantComponent extends Component
{
    use PersonTrait;
    use ProductTrait;
    use RequirementTrait;
    use WithFileUploads;

    protected $aditional_actions;
    protected $arr_actions;

    public $purchase;

    public $modal_supplier = false;

    public $document_type_id, $client_id, $document_id, $currency_id, $type, $schedule_time, $number, $date_time, $total, $exchange_rate, $pending_amount, $comment,$flete,$seguro,$other;
    public $paided_amount;

    public $document_type_name;
    public $branch_office;
    public $supplier;
    public $currency;
    public $document_types, $currencies;
    public $payment_methods;
    public $payments;
    public $document_payments;

    public $search_document;
    public $search_list_document;

    public $search_person;
    public $search_list_people;

    public $search_product;
    public $barcode;
    public $list_search_product;
    public $shopping_car;

    public $image;
    public $imageUploaded;

    protected $listeners = [
        'toggle-modal-supplier' => 'toggle_supplier',
    ];

    public function hydrate()
    {
        $this->resetValidation();
    }

    public function updatedImage()
    {
        //ARCHIVO ADJUNTO: IMAGEN O PDF
        $this->validate([
            'image' => 'nullable|mimes:jpg,jpeg,png,gif,bmp,webp,pdf',
        ]);
    }

    public function mount(Purchase $purchase)
    {
        $this->purchase = $purchase;
        $this->search_list_people = [];
        $this->search_list_document = [];
        $this->list_search_product = [];
        $this->document_payments = [];
        $this->document_types = Purchase::list_document_types();
        $this->currencies = Currency::order_currency()->get();

        $this->document_type_id = $this->document_types[0]->id;
        $this->document_type_name = $this->document_types[0]->name;
        $this->currency_id = $this->currencies[0]->id;
        $this->type = "C";
        $this->date_time = date('Y-m-d');
        $this->total = 0;
        $this->flete = 0;
        $this->other = 0;
        $this->seguro = 0;
        $this->exchange_rate = "1";
        $this->pending_amount = 0;
        $this->comment = "";

        $branch_office = Auth::user()->branch_office;
        $supplier = null;

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
            "operation_number" => "",
            "date" => date("Y-m-d"),
            "comment" => "",
        ];

        if(count($this->purchase->getMedia('images')) > 0){
            $this->imageUploaded = true;
        }

        if($purchase->id != null){
            $this->document_type_id = $this->purchase->document_type_id;
            $this->client_id = $this->purchase->client_id;
            $this->document_id = $this->purchase->document_id;
            $this->document_type_name = DocumentType::find($this->purchase->document_id)->name;
            $this->currency_id = $this->purchase->currency_id;
            $this->type = $this->purchase->type;
            $this->schedule_time = $this->purchase->schedule_time;
            $this->number = $this->purchase->number;
            $this->date_time = date_format(new \DateTime($this->purchase->date_time), 'Y-m-d');
            $this->total = $this->purchase->total;
            $this->flete = $this->purchase->flete;
            $this->seguro = $this->purchase->seguro;
            $this->other = $this->purchase->other;
            $this->exchange_rate = $this->purchase->exchange_rate;
            $this->pending_amount = $this->purchase->pending_amount;
            $this->comment = $this->purchase->comment();

            $this->currency = [
                "symbol" => $this->purchase->currency->symbol,
                "code" => $this->purchase->currency->code,
                "name" => $this->purchase->currency->name,
                "id" => $this->purchase->currency->id,
            ];

            $branch_office = $purchase->branch_office;
            $supplier = $purchase->client;

            $details = $purchase->details;
            foreach ($details as $key => $value) {
                $product = Product::find($value["product_id"]);
                if($product){
                    $this->shopping_car[] = [
                        "product_id" => $value["product_id"],
                        "product_name" => $product->name,
                        "unit_id" => $value["unit_id"],
                        "currency_id" => $value["currency_id"],
                        "units" => $product->all_only_units,
                        "quantity" => sprintf('%g', $value["quantity"]),
                        "purchase_price" => sprintf('%g', $value["purchase_price"])
                    ];
                }
            }
            $this->updatedShoppingCar();
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
        return view('livewire.purchase.purchase.mant-component')->with(compact('person', 'role_id', 'arr_actions'));
    }

    /**
     * Función para regresar
     */
    public function back_button(){
        redirect()->route('purchase');
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
        $total = 0;
        foreach ($this->shopping_car as $key => $value) {
            if(!$value["quantity"] || trim($value["quantity"]) == "" || trim($value["quantity"]) <= 0 ){
                $value["quantity"] = "1";
                $this->shopping_car[$key]["quantity"] = "1";
            }
            if(!$value["purchase_price"] || trim($value["purchase_price"]) == "" || trim($value["purchase_price"]) < 0 ){
                $value["purchase_price"] = "0.00";
                $this->shopping_car[$key]["purchase_price"] = "0.00";
            }
            $total = $total + $value["quantity"] * $value["purchase_price"];
        }
        $total = $this->flete + $this->seguro + $this->other + $total;
        $this->total = $total;
        $this->updatedType();
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
        }
    }

    public function updatingOther($value){
        $this->other = $value;
        $this->updatedShoppingCar();
    }

    public function updatingSeguro($value){
        $this->seguro = $value;
        $this->updatedShoppingCar();
    }

    public function updatingFlete($value){
        $this->flete = $value;
        $this->updatedShoppingCar();
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
     * Funciones para filtro de Requerimiento
     */
    public function updatingSearchDocument($value)
    {
        if($value != ""){
            $this->search_list_document = $this->search_requirement_autocomplete($value);
        }else{
            $this->search_list_document = [];
        }
    }

    public function select_active_document($document_id){
        $document = PurchaseRequirement::find($document_id);
        if($document){
            $this->document_id = $document->id;
            $this->search_document = $document->number;
            $this->shopping_car = [];
            $details = $document->details;
            foreach ($details as $key => $value) {
                $product = Product::find($value["product_id"]);
                $dat = $product->base_product_unit();
                if($product){
                    $this->shopping_car[] = [
                        "product_id" => $value["product_id"],
                        "product_name" => $product->name,
                        "unit_id" => $value["unit_id"],
                        "currency_id" => $value["currency_id"],
                        "units" => $product->all_only_units,
                        "quantity" => sprintf('%g', $value["quantity"]),
                        "purchase_price" => $dat->purchase_price,
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

    public function quit_document(){
        $this->document_id = null;
        $this->search_document = "";
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
        //    $this->list_search_product = [];
        //}
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
                        "unit_id" => $this->list_search_product[$key]["base_unit_id"],
                        "currency_id" => $this->shopping_car[$key2]["currency_id"],
                        "units" => $this->list_search_product[$key]["units"],
                        "quantity" => $this->shopping_car[$key2]["quantity"] + 1,
                        "purchase_price" => $this->shopping_car[$key2]["purchase_price"],
                    ];
                    $this->updatedShoppingCar();
                    return true;
                }
            }
            $this->shopping_car[] = [
                "product_id" => $this->list_search_product[$key]["id"],
                "product_name" => $this->list_search_product[$key]["name"].' '.$this->list_search_product[$key]["model"].' '.$this->list_search_product[$key]["presentation"].' '.$this->list_search_product[$key]['brand_name'],
                "unit_id" => $this->list_search_product[$key]["base_unit_id"],
                "currency_id" => $this->currency_id,
                "units" => $this->list_search_product[$key]["units"],
                "quantity" => 1,
                "purchase_price" => $this->list_search_product[$key]["purchase_price"],
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
                        "product_name" => $product_search["name"].' '.$product_search["model"].' '.$product_search["presentation"].' '.$product['brand_name'],
                        "unit_id" => $product_search["base_unit_id"],
                        "currency_id" => $this->shopping_car[$key2]["currency_id"],
                        "units" => $product_search["units"],
                        "quantity" => $this->shopping_car[$key2]["quantity"] + 1,
                        "purchase_price" => $this->shopping_car[$key2]["purchase_price"],
                    ];
                    $this->updatedShoppingCar();
                    return true;
                }
            }
            $this->shopping_car[] = [
                "product_id" => $product_search["id"],
                "product_name" => $product_search["name"].' '.$product_search["model"].' '.$product_search["presentation"].' '.$product['brand_name'],
                "unit_id" => $product_search["base_unit_id"],
                "currency_id" => $this->currency_id,
                "units" => $product_search["units"],
                "quantity" => 1,
                "purchase_price" => "0.00",
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
                "operation_number" => "",
                "date" => "",
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
        return true;
    }

    /**
     * Función para guardar
     */
    public function save(PurchaseRequest $form_request){
        Validator::make(
            $this->inputs_validation(),
            $form_request->rules(),
            $form_request->messages(),
            $form_request->validationAttributes(),
        )->validate();

        if(!$this->validate_payments()){
            return true;
        }
        DB::beginTransaction();
        try {

            $document_input = array_merge($this->input_create_purchase(), $this->calculate_payments());

            $document = Purchase::create($document_input);

            if($this->image){
                $document->clearMediaCollection('images');
                $arr_original_file_name = explode(".",$this->image->getClientOriginalName());
                $name = "document-". $document->id;
                $file_name = $name . "." . $arr_original_file_name[(count($arr_original_file_name) - 1)];
                $document->addMedia($this->image->getRealPath())->usingName($name)->usingFileName($file_name)->toMediaCollection('images');
            }

            $requirement = PurchaseRequirement::find($this->document_id);
            if($requirement){
                $requirement->update([
                    "document_id" => $document->id,
                    "status" => 'Y'
                ]);
            }


            foreach ($this->shopping_car as $key => $item) {
                $input_detail = [
                    'document_id' => $document->id,
                    'product_id' => $item["product_id"],
                    'unit_id' => $item["unit_id"],
                    'currency_id' => $this->currency_id,
                    'quantity' => $item["quantity"],
                    'purchase_price' => $item["purchase_price"],
                    'dispatched_quantity' => 0,
                    'status' => 'N'
                ];
                DocumentDetail::create($input_detail);
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
                    if(($value["total_amount"]  + 0) > 0){
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


            DB::commit();
            $this->reset(['image']);

            Session::flash('success', 'Se registró correctamente');
            redirect()->route('purchase');


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
        $detail = [
            "comment" => $this->comment ? $this->comment : ""
        ];
        return [
            "type" => $this->type,
            "document_type_id" => $this->document_type_id,
            "document_id" => $this->document_id,
            'client_id' => $this->supplier["id"],
            'user_id' => Auth::user()->id,
            "currency_id" => $this->currency_id,
            'branch_office_id' => Auth::user()->branch_office_id,
            "schedule_time" => $this->schedule_time,
            "date_time" => $this->date_time,
            "number" => $this->number,
            "total" => $this->total,
            "flete" => $this->flete,
            "seguro" => $this->seguro,
            "other" => $this->other,
            "exchange_rate" => $this->exchange_rate,
            "pending_amount" => $this->pending_amount,
            'detail' => json_encode($detail),
            'dispatched' => 'N'
        ];
    }

}
