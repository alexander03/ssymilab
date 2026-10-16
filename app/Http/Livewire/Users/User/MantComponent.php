<?php

namespace App\Http\Livewire\Users\User;

use App\Http\LivewireRequest\Users\User\UserRequestCreate;
use App\Http\LivewireRequest\Users\User\UserRequestUpdate;
use App\Models\BranchOffice;
use App\Models\Person;
use App\Models\Role;
use App\Models\User;
use App\Models\UserType;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Livewire\Component;

class MantComponent extends Component
{
    protected $aditional_actions;
    protected $arr_actions;

    public $modal;
    public $event_modal;

    public $modal_person = false;

    public $user;

    public $username, $password, $password_confirmation, $current_password, $branch_office_id, $user_type_id, $person_id;

    public $people = [];
    public $branch_offices = [];
    public $user_types = [];

    protected $listeners = [
        'toggle-modal-person' => 'toggle_person'
    ];

    public function hydrate(){
        $this->resetValidation();
        $this->emit('select2');
    }

    public function mount(User $user, bool $modal, string $event_modal)
    {
        $this->user = $user;
        $this->modal = $modal;
        $this->event_modal = $event_modal;

        $this->username = $this->user->username;
        $this->branch_office_id = $this->user->branch_office_id;
        $this->user_type_id = $this->user->user_type_id;
        $this->person_id = $this->user->person_id;

        $this->people = Person::with_rol('U')->get();
        $this->branch_offices = BranchOffice::orderBy('name')->get();
        $this->user_types = UserType::orderBy('name')->get();
    }

    public function render()
    {
        $this->load_actions();
        $arr_actions = $this->arr_actions;

        if(!$this->person_id && count($this->people) > 0){
            $this->person_id = $this->people[0]->id;
        }
        if(!$this->branch_office_id && count($this->branch_offices) > 0){
            $this->branch_office_id = $this->branch_offices[0]->id;
        }
        if(!$this->user_type_id && count($this->user_types) > 0){
            $this->user_type_id = $this->user_types[0]->id;
        }
        $person = new Person();
        $role_id = Role::where('code', 'U')->first()->id;
        return view('livewire.users.user.mant-component')->with(compact('person', 'role_id', 'arr_actions'));
    }

    public function back_button(){
        if (!$this->modal){
            redirect()->route('user');
        }else{
            // $this->emit($this->event_modal);
        }
    }

    public function save(UserRequestCreate $form_request){
        $inputs = [
            "username" => $this->username,
            "password" => $this->password,
            "password_confirmation" => $this->password_confirmation,
            "branch_office_id" => $this->branch_office_id,
            "user_type_id" => $this->user_type_id,
            "person_id" => $this->person_id,
        ];

        Validator::make(
            $inputs,
            $form_request->rules(),
            $form_request->messages(),
            $form_request->validationAttributes(),
        )->validate();

        DB::beginTransaction();
        try {
            $user = new User();
            $user->username = $this->username;
            $user->branch_office_id = $this->branch_office_id;
            $user->user_type_id = $this->user_type_id;
            $user->person_id = $this->person_id;
            $user->password = Hash::make($this->password) ;
            $user->save();

            DB::commit();

            if (!$this->modal){
                Session::flash('success', 'Se registró correctamente');
                redirect()->route('user');
            }else{
                $this->reset(['username', 'branch_office_id','user_type_id', 'menu_category_id', 'pasword', 'password_confirmation', 'current_password']);
                $this->emit('alert-success', [
                    'title' => '¡Exito!',
                    'text' => 'Se registró correctamente',
                ]);
                $this->emit($this->event_modal, $user->id);
            }

        } catch (\Exception $th) {

            DB::rollback();

            $this->emit('alert-error', [
                'title' => '¡Error!',
                'text' => 'Algo salio mal: ' . $th->getMessage(),
            ]);
        }
    }

    public function update(UserRequestUpdate $form_request){
        $inputs = [
            "username" => $this->username,
            "password" => $this->password,
            "password_confirmation" => $this->password_confirmation,
            "branch_office_id" => $this->branch_office_id,
            "user_type_id" => $this->user_type_id,
            "person_id" => $this->person_id,
        ];

        Validator::make(
            $inputs,
            $form_request->rules(),
            $form_request->messages(),
            $form_request->validationAttributes(),
        )->validate();

        DB::beginTransaction();
        try {
            $this->user->user_type_id = $this->user_type_id;
            $this->user->person_id = $this->person_id;
            $this->user->branch_office_id = $this->branch_office_id;
            if(strlen($this->password) > 0){
                $this->user->password = Hash::make($this->password) ;
            }
            $this->user->save();

            DB::commit();

            Session::flash('success', 'Se editó correctamente');
            redirect()->route('user');

        } catch (\Exception $th) {

            DB::rollback();

            $this->emit('alert-error', [
                'title' => '¡Error!',
                'text' => 'Algo salio mal: ' . $th->getMessage(),
            ]);
        }
    }

    public function toggle_person ($person_id = null){
        $this->modal_person = !$this->modal_person;
        if($person_id){
            $this->people = Person::with_rol('U')->get();
            $this->person_id = $person_id;
        }
    }

    protected function load_actions(){
        $this->aditional_actions = Auth::user()->user_type->actions()
            ->whereIn('actions.code', ['create_person'])->get();
        $this->arr_actions = [];
        foreach ($this->aditional_actions as $key => $value) {
            $this->arr_actions[] = $value->code;
        }
    }
}
