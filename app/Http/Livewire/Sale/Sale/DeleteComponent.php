<?php

namespace App\Http\Livewire\Sale\Sale;

use App\Models\Sale;
use Livewire\Component;
use Illuminate\Support\Facades\DB;

class DeleteComponent extends Component
{
    public $open = false;

    public $model_id = null;

    protected $listeners = [
        'toggle-modal' => 'toggle_modal'
    ];

    public function toggle_modal($model_id = null){

        $this->open = !$this->open;
        if($model_id){
            $this->model_id = $model_id;
        }

    }

    public function render()
    {
        return view('livewire.sale.sale.delete-component');
    }

    public function delete()
    {
        DB::beginTransaction();
        try {
            $sale = Sale::find($this->model_id);

            $sale->update(["status" => "A"]);

            DB::commit();

            $this->emit('search-and-reset-pages');

            $this->open = false;

            $this->emit('alert-success', [
                'title' => '¡Exito!',
                'text' => 'Se anuló correctamente',
            ]);

        } catch (\Exception $th) {

            DB::rollback();

            $this->emit('alert-error', [
                'title' => '¡Error!',
                'text' => 'Algo salio mal: ' . $th->getMessage(),
            ]);
        }
    }
}
