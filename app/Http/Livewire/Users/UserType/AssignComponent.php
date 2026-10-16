<?php

namespace App\Http\Livewire\Users\UserType;

use App\Models\Action;
use App\Models\MenuCategory;
use App\Models\MenuOption;
use App\Models\Operation;
use App\Models\Permission;
use App\Models\UserType;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Livewire\Component;

class AssignComponent extends Component
{
    public $user_type;

    public $menu_options = [];

    public $actions = [];

    public $all_categories;

    public function mount(UserType $user_type)
    {
        $this->user_type = $user_type;

        $this->all_categories = MenuCategory::orderBy('name')->get();

        foreach ($this->all_categories as $key => $category) {
            $category->menu_options;
            foreach ($category->menu_options as $key => $option) {
                $option->actions;
            }
        }

        foreach ($this->user_type->menu_options as $key => $value) {
            $this->menu_options[] = "".$value->id;
        }
        foreach ($this->user_type->actions as $key => $value) {
            $this->actions[] = "".$value->id;
        }

    }

    public function render()
    {
        return view('livewire.users.user-type.assign-component');
    }

    public function back_button(){
        redirect()->route('user-type');
    }

    public function change_menu_option($menu_option_id){

        try {
            $menu_option = MenuOption::findOrFail($menu_option_id);

            if(in_array("$menu_option_id", $this->menu_options)){
                foreach ($menu_option->actions as $key => $action) {
                    if(!in_array("$action->id", $this->actions)){
                        $this->actions[] = "$action->id";
                    }
                }
            }else{
                $aux = [];
                $arr_actions = [];
                foreach ($menu_option->actions as $key => $action) {
                    $arr_actions[] = "" . $action->id;
                }
                foreach ($this->actions as $key => $value) {
                    if(!in_array($value, $arr_actions)){
                        $aux[] = $value;
                    }
                }
                $this->actions = $aux;
            }

        } catch (\Exception $th) {

            $this->emit('alert-error', [
                'title' => '¡Error!',
                'text' => 'Algo salio mal: ' . $th->getMessage(),
            ]);

        }

    }

    public function change_action($action_id){

        try {
            $action = Action::findOrFail($action_id);

            if(in_array("$action_id", $this->actions)){
                if(!in_array("".$action->menu_option->id, $this->menu_options)){
                    $this->menu_options[] = "".$action->menu_option->id;
                }
            }
        } catch (\Exception $th) {

            $this->emit('alert-error', [
                'title' => '¡Error!',
                'text' => 'Algo salio mal: ' . $th->getMessage(),
            ]);

        }

    }

    public function save(){

        DB::beginTransaction();
        try {
            Permission::where('user_type_id', $this->user_type->id)->delete();

            foreach ($this->menu_options as $key => $menu_option_id) {
                Permission::updateOrCreate([
                    'user_type_id' => $this->user_type->id,
                    'menu_option_id' => $menu_option_id
                ]);
            }

            Operation::where('user_type_id', $this->user_type->id)->delete();

            foreach ($this->actions as $key => $action_id) {
                Operation::updateOrCreate([
                    'user_type_id' => $this->user_type->id,
                    'action_id' => $action_id
                ]);
            }
            // $user_type = UserType::create($inputs);

            DB::commit();

            Session::flash('success', 'Se registró correctamente');
            redirect()->route('user-type');


        } catch (\Exception $th) {

            DB::rollback();

            $this->emit('alert-error', [
                'title' => '¡Error!',
                'text' => 'Algo salio mal: ' . $th->getMessage(),
            ]);
        }
    }
}
