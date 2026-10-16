<?php

namespace App\Http\Livewire\System\TransferMethod;

use App\Http\LivewireRequest\System\TransferMethod\TransferMethodRequest;
use App\Models\TransferMethod;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Livewire\Component;

class MantComponent extends Component
{

    public $modal;
    public $event_modal;

    public $transfer_method;

    public $name;

    public function mount(TransferMethod $transfer_method, bool $modal, string $event_modal)
    {
        $this->transfer_method = $transfer_method;
        $this->modal = $modal;
        $this->event_modal = $event_modal;

        $this->name = $this->transfer_method->name;

    }

    public function render()
    {
        return view('livewire.system.transfer-method.mant-component');
    }

    public function back_button(){
        if (!$this->modal){
            redirect()->route('transfer-method');
        }else{
            // $this->emit($this->event_modal);
        }
    }

    public function save(TransferMethodRequest $form_request){
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

            $transfer_method = TransferMethod::updateOrCreate($inputs);

            DB::commit();

            if (!$this->modal){
                Session::flash('success', 'Se registró correctamente');
                redirect()->route('transfer-method');
            }else{
                $this->reset(['name']);
                $this->emit('alert-success', [
                    'title' => '¡Exito!',
                    'text' => 'Se registró correctamente',
                ]);
                $this->emit($this->event_modal, $transfer_method->id);
            }


        } catch (\Exception $th) {

            DB::rollback();

            $this->emit('alert-error', [
                'title' => '¡Error!',
                'text' => 'Algo salio mal: ' . $th->getMessage(),
            ]);
        }
    }

    public function update(TransferMethodRequest $form_request){
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

            $this->transfer_method->update($inputs);

            DB::commit();

            Session::flash('success', 'Se editó correctamente');
            redirect()->route('transfer-method');

        } catch (\Exception $th) {

            DB::rollback();

            $this->emit('alert-error', [
                'title' => '¡Error!',
                'text' => 'Algo salio mal: ' . $th->getMessage(),
            ]);
        }
    }
}
