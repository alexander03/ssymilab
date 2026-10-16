<?php

namespace App\Http\Livewire\Equipment\Installation;

use App\Models\Schedule;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class ChangeStatusComponent extends Component
{
    public $open = false;

    public $model_id = null;

    protected $listeners = [
        'toggle-modal-status' => 'toggle_modal'
    ];

    public function render()
    {
        $status = "Y";
        if($this->model_id){
            $status = Schedule::find($this->model_id)->product_status;
        }
        return view('livewire.equipment.installation.change-status-component')->with(compact('status'));
    }

    public function toggle_modal($model_id = null){

        $this->open = !$this->open;
        if($model_id){
            $this->model_id = $model_id;
        }

    }

    public function change_status()
    {
        DB::beginTransaction();
        try {
            $product = Schedule::find($this->model_id);

            $product->update(['product_status' => ($product->product_status == "Y") ? "N" : "Y"]);

            DB::commit();

            $this->emit('search-and-reset-pages');

            $this->open = false;

            if($product->status == "Y"){
                $this->emit('alert-success', [
                    'title' => '¡Exito!',
                    'text' => 'Se habilitó correctamente',
                ]);
            }else{
                $this->emit('alert-success', [
                    'title' => '¡Exito!',
                    'text' => 'Se deshabilitó correctamente',
                ]);
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
