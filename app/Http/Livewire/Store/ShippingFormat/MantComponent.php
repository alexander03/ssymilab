<?php

namespace App\Http\Livewire\Store\ShippingFormat;

use App\Http\LivewireRequest\Maintenance\Person\PersonRequest;
use App\Models\Person;
use App\Models\PersonRole;
use App\Models\Role;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Livewire\Component;

class MantComponent extends Component
{
    public $modal;
    public $event_modal;

    public $person;

    public $type, $dni, $ruc;
    public $name, $surname_father, $surname_mother, $business_name;
    public $address, $email, $phone, $code, $shipping_person, $shipping_person_dni;

    public $roles = [];

    public $arr_roles;

    public function mount(Person $person, bool $modal, string $event_modal, string $role_id)
    {
        $this->person = $person;
        $this->modal = $modal;
        $this->event_modal = $event_modal;

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

        if($person->id){
            $roles = $person->roles;
            foreach ($roles as $key => $value) {
                $this->roles[] = $value->id . "";
            }
        }else{
            if($role_id != "") $this->roles[] = $role_id;
            $this->type = "1";
        }

        $this->arr_roles = Role::orderBy('name')->get();
    }

    public function back_button(){
        if (!$this->modal){
            redirect()->route('shipping-format');
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
        return view('livewire.store.shipping-format.mant-component');
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

            DB::commit();

            if (!$this->modal){
                Session::flash('success', 'Se registró correctamente');
                redirect()->route('shipping-format');
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
            ];
            $this->person->update($input_person);

            PersonRole::where('person_id', $this->person->id)->delete();

            foreach ($this->roles as $key => $role_id) {
                PersonRole::updateOrCreate([
                    'person_id' => $this->person->id,
                    'role_id' => $role_id
                ]);
            }

            DB::commit();

            Session::flash('success', 'Se editó correctamente');
            redirect()->route('shipping-format');

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

}
