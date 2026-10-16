<?php

namespace App\Http\Livewire\Maintenance\Reason;

use App\Models\Reason;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class DeleteComponent extends Component
{
    public $open = false;

    public $model_id = null;

    protected $listeners = [
        'toggle-modal' => 'toggle_modal'
    ];

    public function render()
    {
        return view('livewire.maintenance.reason.delete-component');
    }

    public function toggle_modal($model_id = null){

        $this->open = !$this->open;
        if($model_id){
            $this->model_id = $model_id;
        }

    }

    public function delete()
    {
        DB::beginTransaction();
        try {
            $reason = Reason::find($this->model_id);

            if(!$reason){
                throw new \Exception("No existen registros en base de datos.");
            }

            $reason->delete();

            DB::commit();

            $this->emit('search-and-reset-pages');

            $this->open = false;

            $this->emit('alert-success', [
                'title' => '¡Exito!',
                'text' => 'Se eliminó correctamente',
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
