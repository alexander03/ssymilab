<?php

namespace App\Http\Livewire\System\BranchOffice;

use App\Models\BranchOffice;
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
        return view('livewire.system.branch-office.delete-component');
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
            $branch_office = BranchOffice::find($this->model_id);

            if(!$branch_office){
                throw new \Exception("No existen registros en base de datos.");
            }

            if( count($branch_office->users) > 0 ){
                throw new \Exception("Existen usuarios que dependen de este registro.");
            }

            if( count($branch_office->products) > 0 ){
                throw new \Exception("Existen productos que dependen de este registro.");
            }

            $branch_office->delete();

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
