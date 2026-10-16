<?php

namespace App\Http\Livewire\Maintenance\Person;

use App\Http\LivewireRequest\Maintenance\Person\PersonRequest;
use App\Models\Person;
use App\Models\PersonRole;
use App\Models\PersonProduct;
use App\Models\Role;
use App\Models\Product;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use App\Http\Traits\Maintenance\ProductTrait;
use App\Http\Traits\Maintenance\PersonTrait;
use Livewire\Component;

class MantComponent extends Component
{
    use ProductTrait;
    use PersonTrait;

    public $modal;
    public $event_modal;

    public $person;

    public $type, $dni, $ruc;
    public $name, $surname_father, $surname_mother, $business_name;
    public $address, $email, $phone, $code, $shipping_person, $shipping_person_dni;
    public $search_list_product;
    public $supervisors;

    public $search_product_person;
    public $barcode;
    public $person_car;
    public $list_search_product_person;

    public $roles = [];

    public $arr_roles,$bandRole;

    public function mount(Person $person, bool $modal, string $event_modal, string $role_id)
    {
        $this->person = $person;
        $this->modal = $modal;
        $this->event_modal = $event_modal;
        $this->person_car = [];
        $this->search_product_person = "";

        $this->type = $this->person->type;
        $this->dni = $this->person->dni;
        $this->ruc = $this->person->ruc;
        $this->name = $this->person->name;
        $this->surname_father = $this->person->surname_father;
        $this->surname_mother = $this->person->surname_mother;
        $this->business_name = $this->person->business_name;
        $this->address = $this->person->address;
        $this->email = $this->person->email;
        $this->phone = $this->person->phone;
        $this->code = $this->person->code;
        $this->shipping_person = $this->person->shipping_person;
        $this->shipping_person_dni = $this->person->shipping_person_dni;
        $this->supervisor_id = $this->person->supervisor_id;
        $this->bandRole = false;
        if($person->id){
            $roles = $person->roles;
            foreach ($roles as $key => $value) {
                $this->roles[] = $value->id . "";
                if($value->id==7){
                    $this->bandRole = true;
                }
            }
            $products2 = PersonProduct::where('person_id','=',$person->id)->get();
            foreach ($products2 as $key => $value) {
                $pro = Product::find($value->product_id);
                if(!is_null($pro)){
                    $this->person_car[] = [ 
                        "product_id" => $value->product_id,
                        "product_name" => $pro->name,
                        "unit_id" => $pro->base_unit_id,
                        "units" => $pro->all_only_units,
                        "product_type" => $pro->type,
                        "price" => $value->price,
                        "model" => $pro->model,
                        "presentation" => $pro->presentation,
                    ];
                }
            }
        }else{
            if($role_id != "") $this->roles[] = $role_id;
            $this->type = "1";
        }

        $this->arr_roles = Role::orderBy('name')->get();
        $this->search_products_person();
        $this->supervisors = $this->search_autocomplete2(null,['S','A']);
    }

    public function back_button(){
        if (!$this->modal){
            redirect()->route('person');
        }else{
            // $this->emit($this->event_modal);
        }
    }

    public function hydrate()
    {
        $this->resetValidation();
    }



    public function render()
    {
        return view('livewire.maintenance.person.mant-component');
    }


    /**
     * Funciones para buscar productos
     */
    public function search_products_person(){
        $supplier_id = null;
        $type="R";
        $this->list_search_product_person = $this->search_products_fast(null, $this->search_product_person, $supplier_id,$type);
    }

    public function select_products_person($key){
        if(array_key_exists($key, $this->list_search_product_person)){
            $focus_quantity_id = "quantity_".$this->list_search_product_person[$key]["id"];
            $this->emit('focus', [
                'id' => $focus_quantity_id
            ]);
            foreach ($this->person_car as $key2 => $value) {
                if($value["product_id"] == $this->list_search_product_person[$key]["id"]){
                    $this->person_car[$key2] = [
                        "product_id" => $this->list_search_product_person[$key]["id"],
                        "product_name" => $this->list_search_product_person[$key]["name"],
                        "product_type" => $this->list_search_product_person[$key]["type"],
                        "unit_id" => $this->list_search_product_person[$key]["base_unit_id"],
                        "units" => $this->list_search_product_person[$key]["units"],
                        "price" => $this->person_car[$key]["price"],
                        "model" => $this->list_search_product_person[$key]["model"],
                        "presentation" => $this->list_search_product_person[$key]["presentation"],
                    ];
                    return true;
                }
            }
            $this->person_car[] = [
                "product_id" => $this->list_search_product_person[$key]["id"],
                "product_name" => $this->list_search_product_person[$key]["name"],
                "unit_id" => $this->list_search_product_person[$key]["base_unit_id"],
                "units" => $this->list_search_product_person[$key]["units"],
                "product_type" => $this->list_search_product_person[$key]["type"],
                "price" => 0,
                "model" => $this->list_search_product_person[$key]["model"],
                "presentation" => $this->list_search_product_person[$key]["presentation"],
            ];
        }
    }

    public function quit_car_item_person($key){
        unset($this->person_car[$key]);
        $this->product_barcode = "";
        $this->document_detail_key = null;
    }

    public function select_bar_code_person(){
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
            foreach ($this->person_car as $key2 => $value) {
                if($value["product_id"] == $product_search["id"]){
                    $this->person_car[$key2] = [
                        "product_id" => $product_search["id"],
                        "product_name" => $product_search["name"],
                        "unit_id" => $product_search["base_unit_id"],
                        "units" => $product_search["units"],
                        "quantity" => $this->person_car[$key2]["quantity"] + 1,
                    ];
                    return true;
                }
            }
            $this->person_car[] = [
                "product_id" => $product_search["id"],
                "product_name" => $product_search["name"],
                "unit_id" => $product_search["base_unit_id"],
                "units" => $product_search["units"],
                "quantity" => 1,
                "guia" => "",
            ];
        }
    }

    public function save(){

        $form_request = new PersonRequest($this->type);

        Validator::make(
            $this->inputs_validation(),
            $form_request->rules(),
            $form_request->messages(),
            $form_request->validationAttributes(),
        )->validate();

        if($this->type == "1"){
            $this->dni = ($this->dni != "") ? $this->dni : null;
            $this->ruc = null;
            $this->business_name = null;
        }else{
            $this->dni = null;
            $this->name = null;
            $this->surname_father = null;
            $this->surname_mother = null;
        }
        DB::beginTransaction();
        try {
            $input_person = [
                "type" => $this->type,
                "dni" => $this->dni,
                "ruc" => $this->ruc,
                "name" => mb_strtoupper($this->name),
                "surname_father" => mb_strtoupper($this->surname_father),
                "surname_mother" => mb_strtoupper($this->surname_mother),
                "business_name" => mb_strtoupper($this->business_name),
                "address" => mb_strtoupper($this->address),
                "email" => $this->email,
                "phone" => $this->phone,
                "code" => $this->code,
                "shipping_person" => $this->shipping_person,
                "shipping_person_dni" => $this->shipping_person_dni,
                "supervisor_id" => $this->supervisor_id,
            ];
            $person = null;
            if($this->type == "1" && trim($this->dni) != ""){
                $person = Person::where('dni', $this->dni)->first();
            }else if($this->type <> "1"){
                $person = Person::where('ruc', $this->ruc)->first();
            }
            if($person){
                $person->update($input_person);
            }else{
                $person = Person::create($input_person);
            }

            foreach ($this->roles as $key => $role_id) {
                PersonRole::updateOrCreate([
                    'person_id' => $person->id,
                    'role_id' => $role_id
                ]);
            }

            foreach ($this->person_car as $key => $item) {
                PersonProduct::updateOrCreate([
                    'person_id' => $person->id,
                    'product_id' => $item["product_id"],
                    'price' => $item["price"],
                ]);
            }

            DB::commit();

            if (!$this->modal){
                Session::flash('success', 'Se registró correctamente');
                redirect()->route('person');
            }else{
                $this->reset(['dni','ruc', 'name', 'surname_father', 'surname_mother', 'business_name', 'address', 'email', 'phone']);
                $this->emit('alert-success', [
                    'title' => '¡Exito!',
                    'text' => 'Se registró correctamente',
                ]);
                $this->emit($this->event_modal, $person->id);
            }

        } catch (\Exception $th) {

            DB::rollback();

            $this->emit('alert-error', [
                'title' => '¡Error!',
                'text' => 'Algo salio mal: ' . $th->getMessage(),
            ]);
        }
    }

    public function update(){

        $form_request = new PersonRequest($this->type);

        Validator::make(
            $this->inputs_validation(),
            $form_request->rules(),
            $form_request->messages(),
            $form_request->validationAttributes(),
        )->validate();

        if($this->type == "1"){
            $this->dni = ($this->dni != "") ? $this->dni : null;
            $this->ruc = null;
            $this->business_name = null;
        }else{
            $this->dni = null;
            $this->name = null;
            $this->surname_father = null;
            $this->surname_mother = null;
        }

        DB::beginTransaction();
        try {
            $input_person = [
                "type" => $this->type,
                "dni" => $this->dni,
                "ruc" => $this->ruc,
                "name" => mb_strtoupper($this->name),
                "surname_father" => mb_strtoupper($this->surname_father),
                "surname_mother" => mb_strtoupper($this->surname_mother),
                "business_name" => mb_strtoupper($this->business_name),
                "address" => mb_strtoupper($this->address),
                "email" => $this->email,
                "phone" => $this->phone,
                "code" => $this->code,
                "shipping_person" => $this->shipping_person,
                "shipping_person_dni" => $this->shipping_person_dni,
                "supervisor_id" => $this->supervisor_id,
            ];
            $this->person->update($input_person);

            PersonRole::where('person_id', $this->person->id)->delete();

            foreach ($this->roles as $key => $role_id) {
                PersonRole::updateOrCreate([
                    'person_id' => $this->person->id,
                    'role_id' => $role_id
                ]);
            }

            PersonProduct::where('person_id', $this->person->id)->delete();

            foreach ($this->person_car as $key => $item) {
                PersonProduct::updateOrCreate([
                    'person_id' => $this->person->id,
                    'product_id' => $item["product_id"],
                    'price' => $item["price"],
                ]);
            }

            DB::commit();

            Session::flash('success', 'Se editó correctamente');
            redirect()->route('person');

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
            "type" => $this->type,
            "dni" => $this->dni,
            "ruc" => $this->ruc,
            "name" => $this->name,
            "surname_father" => $this->surname_father,
            "surname_mother" => $this->surname_mother,
            "business_name" => $this->business_name,
            "address" => $this->address,
            "email" => $this->email,
            "phone" => $this->phone,
            "roles" => $this->roles,
            "code" => $this->code,
            "supervisor_id" => $this->supervisor_id,
        ];
    }

    public function search_document(){
        if($this->type == "6"){
            if(strlen($this->ruc) == 0){
                $this->emit('alert-error', [
                    'title' => '¡Error!',
                    'text' => 'Debe ingresar un RUC',
                ]);
                return true;
            }elseif (strlen($this->ruc) != 11) {
                $this->emit('alert-error', [
                    'title' => '¡Error!',
                    'text' => 'El RUC debe tener 11 dígitos',
                ]);
                return true;
            }
            $client = new Client();
            $res = $client->request('GET', 'http://157.245.85.164/facturacion/buscaCliente/BuscaClienteRuc.php', [
                'query' =>[
                    'fe' => 'N',
                    'token' => 'qusEj_w7aHEpX',
                    'ruc' => $this->ruc
                ]
            ]);
            if($res->getStatusCode() >= 300){
                $this->emit('alert-error', [
                    'title' => '¡Error!',
                    'text' => 'Error en la consulta, digite manualmente',
                ]);
                return true;
            }else{
                $body = json_decode($res->getBody());
                if($body->code == 0){
                    $this->business_name = $body->RazonSocial;
                    $this->address = $body->Direccion;
                }else{
                    $this->emit('alert-error', [
                        'title' => '¡Error!',
                        'text' => $body->mensaje,
                    ]);
                    return true;
                }
            }
        }else{
            if(strlen($this->dni) == 0){
                $this->emit('alert-error', [
                    'title' => '¡Error!',
                    'text' => 'Debe ingresar un DNI',
                ]);
                return true;
            }elseif (strlen($this->dni) != 8) {
                $this->emit('alert-error', [
                    'title' => '¡Error!',
                    'text' => 'El DNI debe tener 8 dígitos',
                ]);
                return true;
            }
            $client = new Client();
            $res = $client->request('GET', 'https://facturae-garzasoft.com/facturacion/buscaCliente/BuscaCliente2.php', [
                'query' =>[
                    'fe' => 'N',
                    'token' => 'qusEj_w7aHEpX',
                    'dni' => $this->dni
                ]
            ]);
            if($res->getStatusCode() >= 300){
                $this->emit('alert-error', [
                    'title' => '¡Error!',
                    'text' => 'Error en la consulta, digite manualmente',
                ]);
                return true;
            }else{
                $body = json_decode($res->getBody());

                if($body->code == 0){
                    $this->surname_father = $body->apepat;
                    $this->surname_mother = $body->apemat;
                    $this->name = $body->nombres;
                }else{
                    $this->emit('alert-error', [
                        'title' => '¡Error!',
                        'text' => $body->mensaje,
                    ]);
                    return true;
                }
            }
        }

    }

    public function update_role(){
        foreach ($this->roles as $key => $role_id) {
            if($role_id==7){
                $this->bandRole=true;
            }
        }
        dd($this->bandRole);
        return true;
    }
}
