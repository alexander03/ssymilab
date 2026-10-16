<?php

namespace App\Http\Livewire\Purchase\Requirement;

use App\Http\LivewireRequest\Purchase\Requirement\RequirementRequest;
use App\Http\Traits\Maintenance\PersonTrait;
use App\Http\Traits\Maintenance\ProductTrait;
use App\Http\Traits\Purchase\RequirementTrait;
use App\Models\Currency;
use App\Models\DocumentDetail;
use App\Models\Person;
use App\Models\Product;
use App\Models\PurchaseRequirement;
use App\Models\Role;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Livewire\Component;

class MantComponent extends Component
{
    use RequirementTrait;
    use PersonTrait;
    use ProductTrait;

    protected $aditional_actions;
    protected $arr_actions;

    public $requirement;

    public $modal_supplier = false;

    public $client_id, $number, $date_time, $comment, $currency_id;

    public $branch_office;
    public $supplier;
    public $currency;
    public $currencies;

    public $search_person;
    public $search_list_people;

    public $search_product;
    public $barcode;
    public $list_search_product;
    public $shopping_car;

    protected $listeners = [
        'toggle-modal-supplier' => 'toggle_supplier',
    ];

    public function mount(PurchaseRequirement $requirement)
    {
        $this->requirement = $requirement;
        $this->search_list_people = [];
        $this->currencies = Currency::order_currency()->get();
        $this->currency_id = $this->currencies[0]->id;

        $this->currency = [
            "symbol" => $this->currencies[0]->symbol,
            "code" => $this->currencies[0]->code,
            "name" => $this->currencies[0]->name,
            "id" => $this->currencies[0]->id,
        ];

        if($requirement->id != null){
            $details = ($this->requirement->detail && trim($this->requirement->detail) != "") ? json_decode($this->requirement->detail, true) : [];
            $this->client_id = $this->requirement->client_id;
            $this->total = $this->requirement->total;
            $this->comment = (array_key_exists('comment', $details)) ? $details["comment"] : "";
            $this->number = $this->requirement->number;
            $this->date_time = date_format(new \DateTime($this->requirement->date_time), 'Y-m-d');

            $branch_office = $requirement->branch_office;
            $supplier = $requirement->client;
            $details = $requirement->details;
            foreach ($details as $key => $value) {
                $product = Product::find($value["product_id"]);
                if($product){
                    $this->shopping_car[] = [
                        "product_id" => $value["product_id"],
                        "product_name" => $product->name,
                        "presentation" => $product->presentation,
                        "unit_id" => $value["unit_id"],
                        "units" => $product->all_only_units,
                        "quantity" => round($value["quantity"],0),
                        "model" => $product->model,
                        "type" => $product->type,
                    ];
                }
            }

            $this->currency = [
                "symbol" => $requirement->currency->symbol,
                "code" => $requirement->currency->code,
                "name" => $requirement->currency->name,
                "id" => $requirement->currency->id,
            ];

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
    }

    public function render()
    {
        $this->load_actions();
        $arr_actions = $this->arr_actions;

        $person = new Person();
        $role_id = Role::where('code', 'P')->first()->id;
        $this->search_products();
        return view('livewire.purchase.requirement.mant-component')->with(compact('person', 'role_id', 'arr_actions'));
    }

    /**
     * Función para regresar
     */
    public function back_button(){
        redirect()->route('requirement');
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
            $this->search_products();
            $this->shopping_car = [];
        }
    }

    /**
     * Funciones para buscar productos
     */
    public function search_products(){
        //if($this->search_product != ""){
            $supplier_id = null;
            /*if($this->supplier["id"] && $this->supplier["id"] != ""){
                $supplier_id = $this->supplier["id"];
            }*/
            $this->list_search_product = $this->search_products_fast(null, $this->search_product, $supplier_id);
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
                    $this->shopping_car[$key2] = [
                        "product_id" => $this->list_search_product[$key]["id"],
                        "product_name" => $this->list_search_product[$key]["name"],
                        "unit_id" => $this->list_search_product[$key]["base_unit_id"],
                        "units" => $this->list_search_product[$key]["units"],
                        "quantity" => $this->shopping_car[$key2]["quantity"] + 1,
                        "model" => $this->list_search_product[$key]["model"],
                        "type" => $this->list_search_product[$key]["type"],
                        "presentation" => $this->list_search_product[$key]["presentation"]
                    ];
                    return true;
                }
            }
            $this->shopping_car[] = [
                "product_id" => $this->list_search_product[$key]["id"],
                "product_name" => $this->list_search_product[$key]["name"],
                "unit_id" => $this->list_search_product[$key]["base_unit_id"],
                "units" => $this->list_search_product[$key]["units"],
                "quantity" => 1,
                "model" => $this->list_search_product[$key]["model"],
                "type" => $this->list_search_product[$key]["type"],
                "presentation" => $this->list_search_product[$key]["presentation"]
            ];
        }
    }

    public function quit_car_item($key){
        unset($this->shopping_car[$key]);
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
                        "model" => $product_search["model"],
                        "type" => $product_search["type"],
                        "presentation" => $product_search["presentation"]
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
                "model" => $product_search["model"],
                "type" => $product_search["type"],
                "presentation" => $product_search["presentation"]
            ];
        }
    }

    /**
     * Función para guardar
     */
    public function save(RequirementRequest $form_request){
        Validator::make(
            $this->inputs_validation(),
            $form_request->rules(),
            $form_request->messages(),
            $form_request->validationAttributes(),
        )->validate();

        DB::beginTransaction();
        try {
            $document = PurchaseRequirement::create($this->input_create_requirement());

            foreach ($this->shopping_car as $key => $item) {
                $input_detail = [
                    'document_id' => $document->id,
                    'product_id' => $item["product_id"],
                    'unit_id' => $item["unit_id"],
                    'quantity' => $item["quantity"],
                    'currency_id' => $this->currency_id,
                    'status' => 'Y'
                ];
                DocumentDetail::create($input_detail);
            }

            DB::commit();

            Session::flash('success', 'Se registró correctamente');
            redirect()->route('requirement');


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
    public function update(RequirementRequest $form_request){
        Validator::make(
            $this->inputs_validation(),
            $form_request->rules(),
            $form_request->messages(),
            $form_request->validationAttributes(),
        )->validate();

        DB::beginTransaction();
        try {

            $this->requirement->update($this->input_edit_requirement());

            $this->requirement->details->map(function($detail){
                $detail->delete();
            });
            foreach ($this->shopping_car as $key => $item) {
                $input_detail = [
                    'document_id' => $this->requirement->id,
                    'product_id' => $item["product_id"],
                    'unit_id' => $item["unit_id"],
                    'currency_id' => $this->currency_id,
                    'quantity' => $item["quantity"],
                    'status' => 'Y'
                ];
                DocumentDetail::create($input_detail);
            }

            DB::commit();

            Session::flash('success', 'Se editó correctamente');
            redirect()->route('requirement');

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

    private function input_create_requirement(){
        $detail = [
            "comment" => $this->comment ? $this->comment : ""
        ];
        return [
            'type' => "C",
            'client_id' => $this->supplier["id"],
            'user_id' => Auth::user()->id,
            'branch_office_id' => Auth::user()->branch_office_id,
            'number' => $this->getNextNumber(),
            'date_time' => date('Y-m-d H:i:s'),
            'detail' => json_encode($detail),
            'status' => 'P',
            "currency_id" => $this->currency_id,
        ];
    }

    private function input_edit_requirement(){
        $detail = [
            "comment" => $this->comment ? $this->comment : ""
        ];
        return [
            'client_id' => $this->supplier["id"],
            'user_id' => Auth::user()->id,
            'branch_office_id' => Auth::user()->branch_office_id,
            'date_time' => $this->date_time,
            'detail' => json_encode($detail),
            'status' => $this->requirement->status,
            "currency_id" => $this->currency_id,
        ];
    }


}
