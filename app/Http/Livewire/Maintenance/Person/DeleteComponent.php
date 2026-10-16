<?php

namespace App\Http\Livewire\Maintenance\Person;

use App\Models\Person;
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
        return view('livewire.maintenance.person.delete-component');
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
            $person = Person::find($this->model_id);

            if(!$person){
                throw new \Exception("No existen registros en base de datos.");
            }

            if( count($person->users) > 0 ){
                throw new \Exception("Existen usuarios que dependen de este registro.");
            }

            if( count($person->products) > 0 ){
                throw new \Exception("Existen productos que dependen de este registro.");
            }

            $person->delete();

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
