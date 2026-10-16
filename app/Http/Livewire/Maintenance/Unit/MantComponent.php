<?php

namespace App\Http\Livewire\Maintenance\Unit;

use App\Http\LivewireRequest\Maintenance\Unit\UnitRequest;
use App\Models\Unit;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Livewire\Component;

class MantComponent extends Component
{
    public $modal;
    public $event_modal;

    public $unit;

    public $name, $code;

    public function mount(Unit $unit, bool $modal, string $event_modal)
    {
        $this->unit = $unit;
        $this->modal = $modal;
        $this->event_modal = $event_modal;

        $this->name = $this->unit->name;
        $this->code = $this->unit->code;

    }

    public function render()
    {
        return view('livewire.maintenance.unit.mant-component');
    }

    public function back_button(){
        if (!$this->modal){
            redirect()->route('unit');
        }else{
            // $this->emit($this->event_modal);
        }
    }

    public function save(UnitRequest $form_request){
        $inputs = [
            "name" => mb_strtoupper($this->name),
            "code" => mb_strtoupper($this->code),
        ];

        Validator::make(
            $inputs,
            $form_request->rules(),
            $form_request->messages(),
            $form_request->validationAttributes(),
        )->validate();

        DB::beginTransaction();
        try {

            $unit = Unit::create($inputs);

            DB::commit();

            if (!$this->modal){
                Session::flash('success', 'Se registró correctamente');
                redirect()->route('unit');
            }else{
                $this->reset(['name', 'code']);
                $this->emit('alert-success', [
                    'title' => '¡Exito!',
                    'text' => 'Se registró correctamente',
                ]);
                $this->emit($this->event_modal, $unit->id);
            }


        } catch (\Exception $th) {

            DB::rollback();

            $this->emit('alert-error', [
                'title' => '¡Error!',
                'text' => 'Algo salio mal: ' . $th->getMessage(),
            ]);
        }
    }

    public function update(unitRequest $form_request){
        $inputs = [
            "name" => mb_strtoupper($this->name),
            "code" => mb_strtoupper($this->code),
        ];

        Validator::make(
            $inputs,
            $form_request->rules(),
            $form_request->messages(),
            $form_request->validationAttributes(),
        )->validate();

        DB::beginTransaction();
        try {

            $this->unit->update($inputs);

            DB::commit();

            Session::flash('success', 'Se editó correctamente');
            redirect()->route('unit');

        } catch (\Exception $th) {

            DB::rollback();

            $this->emit('alert-error', [
                'title' => '¡Error!',
                'text' => 'Algo salio mal: ' . $th->getMessage(),
            ]);
        }
    }
}
