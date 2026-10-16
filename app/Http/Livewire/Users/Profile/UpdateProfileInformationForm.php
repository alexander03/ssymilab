<?php

namespace App\Http\Livewire\Users\Profile;

use App\Http\LivewireRequest\Users\Profile\UpdateProfileRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Livewire\Component;
use Livewire\WithFileUploads;

class UpdateProfileInformationForm extends Component
{
    use WithFileUploads;

    /**
     * The component's inputs.
     *
     * @var array
     */
    public $inputs = [];

    /**
     * The new avatar for the user.
     *
     * @var mixed
     */
    public $photo;

    public $user_type_name;

    /**
     * Prepare the component.
     *
     * @return void
     */
    public function mount()
    {
        $user = Auth::user();
        $person = $user->person;
        $this->user_type_name = $user->user_type->name;
        $this->inputs = [
            'type' => $person->type,
            'dni' => $person->dni,
            'ruc'=> $person->ruc,
            'name'=> $person->name,
            'surname_father'=> $person->surname_father,
            'surname_mother'=> $person->surname_mother,
            'business_name'=> $person->business_name,
            'address'=> $person->address,
            'email'=> $person->email,
            'phone'=> $person->phone,
        ];
    }

    public function updateProfileInformation(UpdateProfileRequest $form_request)
    {
        Validator::make(
            $this->inputs,
            $form_request->rules(),
            $form_request->messages(),
            $form_request->validationAttributes(),
        )->validate();

        if($this->inputs["type"] == "1"){
            $this->inputs["ruc"] = null;
            $this->inputs["business_name"] = null;
        }else{
            $this->inputs["dni"] = null;
            $this->inputs["name"] = null;
            $this->inputs["surname_father"] = null;
            $this->inputs["surname_mother"] = null;
        }

        DB::beginTransaction();
        try {
            $person = Auth::user()->person;
            $person->update($this->inputs);

            DB::commit();

            $this->emit('alert-success', [
                'title' => '¡Exito!',
                'text' => 'Se guardó exitosamente.',
            ]);
            $this->emitSelf('saved');
            $this->emit('update-profile');

        } catch (\Exception $th) {

            DB::rollback();

            $this->emit('alert-error', [
                'title' => '¡Error!',
                'text' => 'Algo salio mal: ' . $th->getMessage(),
            ]);
        }
    }

    /**
     * Delete user's profile photo.
     *
     * @return void
     */
    public function deleteProfilePhoto()
    {
        Auth::user()->deleteProfilePhoto();

        $this->emit('update-profile');
    }

    /**
     * Render the component.
     *
     * @return \Illuminate\View\View
     */
    public function render()
    {
        return view('livewire.users.profile.update-profile-information-form');
    }
}
