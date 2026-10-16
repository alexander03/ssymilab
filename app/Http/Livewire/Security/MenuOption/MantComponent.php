<?php

namespace App\Http\Livewire\Security\MenuOption;

use App\Http\LivewireRequest\Security\MenuOption\MenuOptionRequest;
use App\Models\MenuCategory;
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

    public $modal_menu_category = false;

    public $menu_option;

    public $name, $link, $order, $menu_category_id;

    public $menu_categories;

    protected $listeners = [
        'toggle-modal-menu-category' => 'toggle_menu_category'
    ];

    public function mount(MenuOption $menu_option, bool $modal, string $event_modal)
    {
        $this->menu_option = $menu_option;
        $this->modal = $modal;
        $this->event_modal = $event_modal;

        $this->menu_category_id = $this->menu_option->menu_category_id;
        $this->name = $this->menu_option->name;
        $this->link = $this->menu_option->link;
        $this->order = $this->menu_option->order;

        $this->menu_categories = MenuCategory::orderBy('name')->get();
    }

    public function render()
    {
        $this->load_actions();
        $arr_actions = $this->arr_actions;

        if(!$this->menu_category_id){
            $this->menu_category_id = $this->menu_categories[0]->id;
        }
        $menu_category = new MenuCategory();
        return view('livewire.security.menu-option.mant-component')->with(compact('menu_category', 'arr_actions'));
    }

    public function back_button(){
        if (!$this->modal){
            redirect()->route('menu-option');
        }else{
            // $this->emit($this->event_modal);
        }
    }

    public function save(MenuOptionRequest $form_request){
        $inputs = [
            "menu_category_id" => $this->menu_category_id,
            "name" => mb_strtoupper($this->name),
            "link" => $this->link,
            "order" => $this->order,
        ];

        Validator::make(
            $inputs,
            $form_request->rules(),
            $form_request->messages(),
            $form_request->validationAttributes(),
        )->validate();

        DB::beginTransaction();
        try {

            $menu_option = MenuOption::create($inputs);

            DB::commit();

            if (!$this->modal){
                Session::flash('success', 'Se registró correctamente');
                redirect()->route('menu-option');
            }else{
                $this->reset(['name', 'link','order', 'menu_category_id']);
                $this->emit('alert-success', [
                    'title' => '¡Exito!',
                    'text' => 'Se registró correctamente',
                ]);
                $this->emit($this->event_modal, $menu_option->id);
            }

        } catch (\Exception $th) {

            DB::rollback();

            $this->emit('alert-error', [
                'title' => '¡Error!',
                'text' => 'Algo salio mal: ' . $th->getMessage(),
            ]);
        }
    }

    public function update(MenuOptionRequest $form_request){
        $inputs = [
            "menu_category_id" => $this->menu_category_id,
            "name" => mb_strtoupper($this->name),
            "link" => $this->link,
            "order" => $this->order,
        ];

        Validator::make(
            $inputs,
            $form_request->rules(),
            $form_request->messages(),
            $form_request->validationAttributes(),
        )->validate();

        DB::beginTransaction();
        try {

            $this->menu_option->update($inputs);

            DB::commit();

            Session::flash('success', 'Se editó correctamente');
            redirect()->route('menu-option');

        } catch (\Exception $th) {

            DB::rollback();

            $this->emit('alert-error', [
                'title' => '¡Error!',
                'text' => 'Algo salio mal: ' . $th->getMessage(),
            ]);
        }
    }

    public function toggle_menu_category ($menu_category_id = null){
        $this->modal_menu_category = !$this->modal_menu_category;
        if($menu_category_id){
            $this->menu_categories = MenuCategory::orderBy('name')->get();
            $this->menu_category_id = $menu_category_id;
        }
    }

    protected function load_actions(){
        $this->aditional_actions = Auth::user()->user_type->actions()
            ->whereIn('actions.code', ['create_menu_category'])->get();
        $this->arr_actions = [];
        foreach ($this->aditional_actions as $key => $value) {
            $this->arr_actions[] = $value->code;
        }
    }
}
