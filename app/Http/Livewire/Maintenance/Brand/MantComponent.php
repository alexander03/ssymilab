<?php

namespace App\Http\Livewire\Maintenance\Brand;

use App\Http\LivewireRequest\Maintenance\Brand\BrandRequest;
use App\Models\Brand;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Livewire\Component;

class MantComponent extends Component
{
    public $modal;
    public $event_modal;

    public $brand;

    public $name;

    public function mount(Brand $brand, bool $modal, string $event_modal)
    {
        $this->brand = $brand;
        $this->modal = $modal;
        $this->event_modal = $event_modal;

        $this->name = $this->brand->name;

    }

    public function render()
    {
        return view('livewire.maintenance.brand.mant-component');
    }

    public function back_button(){
        if (!$this->modal){
            redirect()->route('brand');
        }else{
            // $this->emit($this->event_modal);
        }
    }

    public function save(BrandRequest $form_request){
        $inputs = [
            "name" => mb_strtoupper($this->name),
        ];

        Validator::make(
            $inputs,
            $form_request->rules(),
            $form_request->messages(),
            $form_request->validationAttributes(),
        )->validate();

        DB::beginTransaction();
        try {

            $brand = Brand::create($inputs);

            DB::commit();

            if (!$this->modal){
                Session::flash('success', 'Se registró correctamente');
                redirect()->route('brand');
            }else{
                $this->reset(['name']);
                $this->emit('alert-success', [
                    'title' => '¡Exito!',
                    'text' => 'Se registró correctamente',
                ]);
                $this->emit($this->event_modal, $brand->id);
            }


        } catch (\Exception $th) {

            DB::rollback();

            $this->emit('alert-error', [
                'title' => '¡Error!',
                'text' => 'Algo salio mal: ' . $th->getMessage(),
            ]);
        }
    }

    public function update(BrandRequest $form_request){
        $inputs = [
            "name" => mb_strtoupper($this->name),
        ];

        Validator::make(
            $inputs,
            $form_request->rules(),
            $form_request->messages(),
            $form_request->validationAttributes(),
        )->validate();

        DB::beginTransaction();
        try {

            $this->brand->update($inputs);

            DB::commit();

            Session::flash('success', 'Se editó correctamente');
            redirect()->route('brand');

        } catch (\Exception $th) {

            DB::rollback();

            $this->emit('alert-error', [
                'title' => '¡Error!',
                'text' => 'Algo salio mal: ' . $th->getMessage(),
            ]);
        }
    }
}
