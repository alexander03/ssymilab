<?php

namespace App\Http\Livewire\Equipment\Installation;

use App\Models\Schedule;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class FilesComponent extends Component
{
    public $open = false;

    public $model_id = null;

    protected $listeners = [
        'toggle-modal-files' => 'toggle_modal_files'
    ];

    public function render()
    {
        $schedule = null;
        if($this->model_id){
            $schedule = Schedule::find($this->model_id);
        }
        return view('livewire.equipment.installation.files-component')->with(compact('schedule'));
    }

    public function toggle_modal_files($model_id = null){

        $this->open = !$this->open;
        if($model_id){
            $this->model_id = $model_id;
        }

    }

    public function delete_file($name)
    {
        DB::beginTransaction();
        try {
            $schedule = Schedule::find($this->model_id);

            foreach ($schedule->getMedia('installation') as $key => $value) {
                if($value->name==$name){
                    $value->delete();
                }
            }

            DB::commit();

            $this->emit('search-and-reset-pages');

            $this->open = false;

        
            $this->emit('alert-success', [
                'title' => '¡Exito!',
                'text' => 'Se habilitó correctamente',
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
