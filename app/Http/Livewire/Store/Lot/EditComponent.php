<?php

namespace App\Http\Livewire\Store\Lot;

use App\Http\Traits\General\StockTrait;
use App\Models\Lot;
use App\Models\StoreDocument;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use App\Models\ProductMove;

class EditComponent extends Component
{
    use StockTrait;

    public $open = false;

    public $model_id = null;

    public $name_lot,$due_date,$barcode;

    protected $listeners = [
        'toggle-modal-edit' => 'toggle_modal'
    ];

    public function render()
    {
        $lot = null;
        if($this->model_id != null){
            $lot = Lot::find($this->model_id);
            $this->name_lot = $lot->lot;
            $this->due_date = $lot->due_date;
            $this->barcode = $lot->barcode;
        }
        return view('livewire.store.lot.edit-component')->with(compact('lot'));
    }

    public function toggle_modal($model_id = null){

        $this->open = !$this->open;
        if($model_id){
            $this->model_id = $model_id;
            $this->name_lot = "";
            $this->due_date = "";
            $this->barcode = "";
        }

    }

    public function save()
    {
        DB::beginTransaction();
        try {

            $lot = Lot::find($this->model_id);

            $lot->lot = $this->name_lot;
            $lot->due_date = $this->due_date;
            $lot->barcode = $this->barcode;

            $lot->save();

            DB::commit();

            $this->emit('search-and-reset-pages');

            $this->open = false;

            $this->emit('alert-success', [
                'title' => '¡Exito!',
                'text' => 'Se registró correctamente',
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
