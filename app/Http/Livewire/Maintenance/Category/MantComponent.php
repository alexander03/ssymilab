<?php

namespace App\Http\Livewire\Maintenance\Category;

use App\Http\LivewireRequest\Maintenance\Category\CategoryRequest;
use App\Models\Category;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Livewire\Component;

class MantComponent extends Component
{
    public $modal;
    public $event_modal;

    public $category;

    public $name;

    public function mount(Category $category, bool $modal, string $event_modal)
    {
        $this->category = $category;
        $this->modal = $modal;
        $this->event_modal = $event_modal;

        $this->name = $this->category->name;

    }

    public function render()
    {
        return view('livewire.maintenance.category.mant-component');
    }

    public function back_button(){
        if (!$this->modal){
            redirect()->route('category');
        }else{
            // $this->emit($this->event_modal);
        }
    }

    public function save(CategoryRequest $form_request){
        $inputs = [
            "name" => mb_strtoupper($this->name),
        ];

        Validator::make(
            $inputs,
            $form_request->rules(),
            $form_request->messages(),
            $form_request->validationAttributes(),
        )->validate();

        DB::beginTransaction();
        try {

            $category = Category::create($inputs);

            DB::commit();

            if (!$this->modal){
                Session::flash('success', 'Se registró correctamente');
                redirect()->route('category');
            }else{
                $this->reset(['name']);
                $this->emit('alert-success', [
                    'title' => '¡Exito!',
                    'text' => 'Se registró correctamente',
                ]);
                $this->emit($this->event_modal, $category->id);
            }


        } catch (\Exception $th) {

            DB::rollback();

            $this->emit('alert-error', [
                'title' => '¡Error!',
                'text' => 'Algo salio mal: ' . $th->getMessage(),
            ]);
        }
    }

    public function update(CategoryRequest $form_request){
        $inputs = [
            "name" => mb_strtoupper($this->name),
        ];

        Validator::make(
            $inputs,
            $form_request->rules(),
            $form_request->messages(),
            $form_request->validationAttributes(),
        )->validate();

        DB::beginTransaction();
        try {

            $this->category->update($inputs);

            DB::commit();

            Session::flash('success', 'Se editó correctamente');
            redirect()->route('category');

        } catch (\Exception $th) {

            DB::rollback();

            $this->emit('alert-error', [
                'title' => '¡Error!',
                'text' => 'Algo salio mal: ' . $th->getMessage(),
            ]);
        }
    }
}
