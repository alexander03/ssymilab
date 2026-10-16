<?php

namespace App\Http\Livewire\Security\MenuCategory;

use App\Http\LivewireRequest\Security\MenuCategory\MenuCategoryRequest;
use App\Models\MenuCategory;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Livewire\Component;

class MantComponent extends Component
{
    public $modal;
    public $event_modal;

    public $menu_category;

    public $name, $icon, $order;

    public function mount(MenuCategory $menu_category, bool $modal, string $event_modal)
    {
        $this->menu_category = $menu_category;
        $this->modal = $modal;
        $this->event_modal = $event_modal;

        $this->name = $this->menu_category->name;
        $this->icon = $this->menu_category->icon ?? "fas fa-plus";
        $this->order = $this->menu_category->order;

    }

    public function render()
    {
        return view('livewire.security.menu-category.mant-component');
    }

    public function back_button(){
        if (!$this->modal){
            redirect()->route('menu-category');
        }else{
            // $this->emit($this->event_modal);
        }
    }

    public function save(MenuCategoryRequest $form_request){
        $inputs = [
            "name" => mb_strtoupper($this->name),
            "icon" => $this->icon,
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

            $menu_category = MenuCategory::create($inputs);

            DB::commit();

            if (!$this->modal){
                Session::flash('success', 'Se registró correctamente');
                redirect()->route('menu-category');
            }else{
                $this->reset(['name', 'icon','order']);
                $this->emit('alert-success', [
                    'title' => '¡Exito!',
                    'text' => 'Se registró correctamente',
                ]);
                $this->emit($this->event_modal, $menu_category->id);
            }


        } catch (\Exception $th) {

            DB::rollback();

            $this->emit('alert-error', [
                'title' => '¡Error!',
                'text' => 'Algo salio mal: ' . $th->getMessage(),
            ]);
        }
    }

    public function update(MenuCategoryRequest $form_request){
        $inputs = [
            "name" => mb_strtoupper($this->name),
            "icon" => $this->icon,
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

            $this->menu_category->update($inputs);

            DB::commit();

            Session::flash('success', 'Se editó correctamente');
            redirect()->route('menu-category');

        } catch (\Exception $th) {

            DB::rollback();

            $this->emit('alert-error', [
                'title' => '¡Error!',
                'text' => 'Algo salio mal: ' . $th->getMessage(),
            ]);
        }
    }
}
