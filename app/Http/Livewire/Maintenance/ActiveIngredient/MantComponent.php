<?php

namespace App\Http\Livewire\Maintenance\ActiveIngredient;

use App\Http\LivewireRequest\Maintenance\ActiveIngredient\ActiveIngredientRequest;
use App\Models\ActiveIngredient;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Livewire\Component;

class MantComponent extends Component
{
    public $modal;
    public $event_modal;

    public $active_ingredient;

    public $name;

    public function mount(ActiveIngredient $active_ingredient, bool $modal, string $event_modal)
    {
        $this->active_ingredient = $active_ingredient;
        $this->modal = $modal;
        $this->event_modal = $event_modal;

        $this->name = $this->active_ingredient->name;

    }

    public function render()
    {
        return view('livewire.maintenance.active-ingredient.mant-component');
    }

    public function back_button(){
        if (!$this->modal){
            redirect()->route('active-ingredient');
        }else{
            // $this->emit($this->event_modal);
        }
    }

    public function save(ActiveIngredientRequest $form_request){
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

            $active_ingredient = ActiveIngredient::create($inputs);

            DB::commit();

            if (!$this->modal){
                Session::flash('success', 'Se registró correctamente');
                redirect()->route('active-ingredient');
            }else{
                $this->reset(['name']);
                $this->emit('alert-success', [
                    'title' => '¡Exito!',
                    'text' => 'Se registró correctamente',
                ]);
                $this->emit($this->event_modal, $active_ingredient->id);
            }


        } catch (\Exception $th) {

            DB::rollback();

            $this->emit('alert-error', [
                'title' => '¡Error!',
                'text' => 'Algo salio mal: ' . $th->getMessage(),
            ]);
        }
    }

    public function update(ActiveIngredientRequest $form_request){
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

            $this->active_ingredient->update($inputs);

            DB::commit();

            Session::flash('success', 'Se editó correctamente');
            redirect()->route('active-ingredient');

        } catch (\Exception $th) {

            DB::rollback();

            $this->emit('alert-error', [
                'title' => '¡Error!',
                'text' => 'Algo salio mal: ' . $th->getMessage(),
            ]);
        }
    }
}
