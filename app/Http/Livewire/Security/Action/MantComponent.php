<?php

namespace App\Http\Livewire\Security\Action;

use App\Http\LivewireRequest\Security\Action\ActionRequest;
use App\Models\Action;
use App\Models\MenuOption;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Livewire\Component;

class MantComponent extends Component
{
    protected $aditional_actions;
    protected $arr_actions;

    public $modal;
    public $event_modal;

    public $modal_menu_option = false;

    public $action;

    public $name, $code, $route, $description, $menu_option_id;

    public $menu_options;

    protected $listeners = [
        'toggle-modal-menu-option' => 'toggle_menu_option'
    ];

    public function mount(Action $action, bool $modal, string $event_modal)
    {
        $this->action = $action;
        $this->modal = $modal;
        $this->event_modal = $event_modal;

        $this->menu_option_id = $this->action->menu_option_id;
        $this->name = $this->action->name;
        $this->code = $this->action->code;
        $this->route = $this->action->route;
        $this->description = $this->action->description;

        $this->menu_options = MenuOption::orderBy('name')->get();
    }

    public function render()
    {
        $this->load_actions();
        $arr_actions = $this->arr_actions;

        if(!$this->menu_option_id){
            $this->menu_option_id = $this->menu_options[0]->id;
        }
        //PARA EL MODAL
        $menu_option = new MenuOption();
        return view('livewire.security.action.mant-component')->with(compact('menu_option', 'arr_actions'));
    }

    public function back_button(){
        if (!$this->modal){
            redirect()->route('action');
        }else{
            // $this->emit($this->event_modal);
        }
    }

    public function save(ActionRequest $form_request){
        $inputs = [
            "menu_option_id" => $this->menu_option_id,
            "name" => mb_strtoupper($this->name),
            "code" => mb_strtolower($this->code),
            "route" => mb_strtolower($this->route),
            "description" => $this->description,
        ];

        Validator::make(
            $inputs,
            $form_request->rules(),
            $form_request->messages(),
            $form_request->validationAttributes(),
        )->validate();

        DB::beginTransaction();
        try {

            $action = Action::create($inputs);

            DB::commit();

            if (!$this->modal){
                Session::flash('success', 'Se registró correctamente');
                redirect()->route('action');
            }else{
                $this->reset(['name', 'code','route', 'description', 'menu_option_id']);
                $this->emit('alert-success', [
                    'title' => '¡Exito!',
                    'text' => 'Se registró correctamente',
                ]);
                $this->emit($this->event_modal, $action->id);
            }

        } catch (\Exception $th) {

            DB::rollback();

            $this->emit('alert-error', [
                'title' => '¡Error!',
                'text' => 'Algo salio mal: ' . $th->getMessage(),
            ]);
        }
    }

    public function update(ActionRequest $form_request){
        $inputs = [
            "menu_option_id" => $this->menu_option_id,
            "name" => mb_strtoupper($this->name),
            "code" => mb_strtolower($this->code),
            "route" => mb_strtolower($this->route),
            "description" => $this->description,
        ];

        Validator::make(
            $inputs,
            $form_request->rules(),
            $form_request->messages(),
            $form_request->validationAttributes(),
        )->validate();

        DB::beginTransaction();
        try {

            $this->action->update($inputs);

            DB::commit();

            Session::flash('success', 'Se editó correctamente');
            redirect()->route('action');

        } catch (\Exception $th) {

            DB::rollback();

            $this->emit('alert-error', [
                'title' => '¡Error!',
                'text' => 'Algo salio mal: ' . $th->getMessage(),
            ]);
        }
    }

    public function toggle_menu_option ($menu_option_id = null){
        $this->modal_menu_option = !$this->modal_menu_option;
        if($menu_option_id){
            $this->menu_options = MenuOption::orderBy('name')->get();
            $this->menu_option_id = $menu_option_id;
        }
    }

    protected function load_actions(){
        $this->aditional_actions = Auth::user()->user_type->actions()
            ->whereIn('actions.code', ['create_menu_option'])->get();

        $this->arr_actions = [];
        foreach ($this->aditional_actions as $key => $value) {
            $this->arr_actions[] = $value->code;
        }
    }
}
