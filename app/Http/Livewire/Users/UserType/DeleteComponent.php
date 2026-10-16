<?php

namespace App\Http\Livewire\Users\UserType;

use App\Models\UserType;
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
        return view('livewire.users.user-type.delete-component');
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
            $user_type = UserType::find($this->model_id);

            if(!$user_type){
                throw new \Exception("No existen registros en base de datos.");
            }

            if( count($user_type->actions) > 0 || count($user_type->menu_options) > 0 || count($user_type->users) > 0){
                throw new \Exception("Existen algún registro que depende de este registro.");
            }

            $user_type->delete();

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
