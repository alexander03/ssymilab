<?php

namespace App\Http\Livewire\Equipment\Installation;

use Livewire\Component;
use App\Models\Job;
use App\Models\Schedule;
use App\Models\Product;
use App\Models\Person;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use App\Http\Traits\Maintenance\PersonTrait;
use App\Http\Traits\Maintenance\ProductTrait;
use App\Http\Traits\Store\SerieTrait;
use Illuminate\Support\Facades\Auth;

class MantComponent extends Component
{
	use PersonTrait;
	use SerieTrait;

	public $modal;
    public $event_modal;

    public $product;
    public $schedule;
    public $customer;

    public $product_name;
    public $installed_date;
    public $product_location;
    public $product_place;
    public $customer_name;
    public $product_serie_id;
    public $status,$list_status;

    public $search_list_people;
    public $search_list_product;

    public function mount(Schedule $schedule, Product $product, bool $modal, string $event_modal)
    {
        $this->product = $product;
        $this->schedule = $schedule;
        $this->modal = $modal;
        $this->event_modal = $event_modal;
        $this->search_list_people = [];
        $this->search_list_product = [];
        $this->product_serie_id = 0;
        $this->status = '';

        $this->product_name = $product->name;
        if($schedule->id != null){
            $this->installed_date = $schedule->installed_date;
            $this->customer_name = $schedule->customer_name;
            $this->product_place = $schedule->product_place;
            $this->product_location = $schedule->product_location;
            $this->product_serie_id = $schedule->product_serie_id;
            $this->status = $schedule->status;
            $product2 = Product::where('id', $schedule->product_serie->product_id)->withTrashed()->first();
            $this->product_name = $product2->code." ".$product2->name." ".$product2->model." | ".$schedule->product_serie->serie;
            $customer = $schedule->customer;
        }else{
            $this->installed_date = "";
            $this->customer_name = "";
            $this->product_place = "";
            $this->product_location = "";
            $customer = null;
        }

        $this->customer = [
            'id' => ($customer) ? $customer->id : "",
            'type' => ($customer) ? $customer->type : "6",
            'name_complete' => ($customer) ? $customer->getNameComplete() : "",
            'document_number' => ($customer) ? $customer->getDocumentNumber() : "",
        ];

        $this->customer_name = $this->customer["name_complete"];
        $this->list_status = ["C" => "CESION DE USO", "V" => "VENDIDO", "P"=>"PRESTAMO"];

    }

    public function render()
    {
        if(!$this->status){
            $this->status = "C";
        }
        return view('livewire.equipment.installation.mant-component');
    }


    public function back_button(){
        if (!$this->modal){
            redirect()->route('installation');
        }else{
            // $this->emit($this->event_modal);
        }
    }

    /**
     * Funciones para filtro de Personas
     */
    public function updatingCustomerName($value)
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
            $this->customer = [
                "id" => $person->id,
                'type' => $person->type,
                "name_complete" => $person->getNameComplete(),
                "document_number" => $person->getDocumentNumber()
            ];
            $this->customer_name = $this->customer["name_complete"];
        }
    }

    /**
     * Funciones para filtro de Producto
     */
    public function updatingProductName($value)
    {
        if($value != ""){
            $this->search_list_product = $this->search_autocompleteSerie(null, '', '', 'Y', Auth::user()->branch_office_id, $value,null,'M');
        }else{
            $this->search_list_product = [];
        }
    }

    public function select_active_product($product_id){
        $product = $this->search_autocompleteSerie(null, '', '', 'Y', Auth::user()->branch_office_id, null,$product_id);
        if($product){
        	$this->product_serie_id = $product_id;
            $this->product_name = $product[0]["product_name"] . " - " . $product[0]["serie"];
        }
    }

    /**
     * Funcion para guardar
     */
    private function validate_product_serie(){
        if($this->product_serie_id==0){
            $this->emit('alert-error', [
                'title' => 'Error!',
                'text' => 'No ha seleccionado ninguno producto con serie',
            ]);
            return false;
        }
        return true;
    }

    public function save(){
        if(!$this->validate_product_serie()){
            return true;
        }

        $inputs = [
            "product_id" => $this->product->id,
            "installed_date" => $this->installed_date,
            "customer_name" => $this->customer_name,
            "customer_id" => $this->customer["id"],
            "product_serie_id" => $this->product_serie_id,
            "duration" => 0,
            "product_status" => "Y",
            "product_place" => $this->product_place,
            "product_location" => $this->product_location,
            "status" => $this->status,
        ];

        // Validator::make(
        //     $inputs,
        //     $form_request->rules(),
        //     $form_request->messages(),
        //     $form_request->validationAttributes(),
        // )->validate();

        DB::beginTransaction();
        try {

            if(!$this->schedule->id){
                $schedule = Schedule::create($inputs);
            }else{
                $schedule = Schedule::find($this->schedule->id);
                $schedule->update($inputs);
            }

            DB::commit();

            if (!$this->modal){
                Session::flash('success', 'Se registró correctamente');
                redirect()->route('installation');
            }


        } catch (\Exception $th) {

            DB::rollback();

            $this->emit('alert-error', [
                'title' => '¡Error!',
                'text' => 'Algo salio mal: ' . $th->getMessage(),
            ]);
        }
    }
}
