<?php

namespace App\Http\Livewire\Users\UserType;

use App\Http\LivewireRequest\Users\UserType\UserTypeRequest;
use App\Models\UserType;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Livewire\Component;

class MantComponent extends Component
{
    public $user_type;

    public $name, $code;

    public function mount(UserType $user_type)
    {
        $this->user_type = $user_type;

        $this->name = $this->user_type->name;
        $this->code = $this->user_type->code;

    }

    public function render()
    {
        return view('livewire.users.user-type.mant-component');
    }

    public function back_button(){
        redirect()->route('user-type');
    }

    public function save(UserTypeRequest $form_request){
        $inputs = [
            "name" => mb_strtoupper($this->name),
            "code" => mb_strtoupper($this->code),
        ];

        Validator::make(
            $inputs,
            $form_request->rules(),
            $form_request->messages(),
            $form_request->validationAttributes(),
        )->validate();

        DB::beginTransaction();
        try {

            $user_type = UserType::create($inputs);

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

    public function update(UserTypeRequest $form_request){
        $inputs = [
            "name" => mb_strtoupper($this->name),
            "code" => mb_strtoupper($this->code),
        ];

        Validator::make(
            $inputs,
            $form_request->rules(),
            $form_request->messages(),
            $form_request->validationAttributes(),
        )->validate();

        DB::beginTransaction();
        try {

            $this->user_type->update($inputs);

            DB::commit();

            Session::flash('success', 'Se editó correctamente');
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
