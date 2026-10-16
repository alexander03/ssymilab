<?php

namespace Tests\Feature\Http\Livewire\Users\Profile;

use App\Http\Livewire\Users\Profile\UpdateProfileInformationForm;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Livewire\Livewire;
use Tests\TestCase;

class UpdateProfileInformationFormTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function setUp() : void
    {
        parent::setUp();

        /** @var \Illuminate\Contracts\Auth\Authenticatable $user */
        $this->user = User::find(1);
    }

    /** @test */
    public function can_see_profile_information_view(){

        $this->actingAs($this->user);

        Livewire::test(UpdateProfileInformationForm::class)
            ->assertViewIs('livewire.users.profile.update-profile-information-form');

    }

    /** @test */
    public function initial_data_is_the_user_data(){
        $this->actingAs($this->user);

        Livewire::test(UpdateProfileInformationForm::class)
            ->assertSet('user_type_name', $this->user->user_type->name)
            ->assertSet('inputs.type', $this->user->person->type)
            ->assertSet('inputs.dni', $this->user->person->dni)
            ->assertSet('inputs.ruc', $this->user->person->ruc)
            ->assertSet('inputs.name', $this->user->person->name)
            ->assertSet('inputs.surname_father', $this->user->person->surname_father)
            ->assertSet('inputs.surname_mother', $this->user->person->surname_mother)
            ->assertSet('inputs.business_name', $this->user->person->business_name)
            ->assertSet('inputs.address', $this->user->person->address)
            ->assertSet('inputs.email', $this->user->person->email)
            ->assertSet('inputs.phone', $this->user->person->phone);
    }

    /** @test */
    public function validate_if_type_document_is_required(){
        $this->actingAs($this->user);

        Livewire::test(UpdateProfileInformationForm::class)
            ->set('inputs.type', "")
            ->call('updateProfileInformation')
            ->assertHasErrors(
                ['type' => 'required']
            );
    }

    /** @test */
    public function validate_if_type_document_is_dni_or_ruc(){
        $this->actingAs($this->user);

        Livewire::test(UpdateProfileInformationForm::class)
            ->set('inputs.type', "5")
            ->call('updateProfileInformation')
            ->assertHasErrors(
                ['type' => 'in']
            );
    }

    /** @test */
    public function validate_required_params_with_dni(){
        $this->actingAs($this->user);

        Livewire::test(UpdateProfileInformationForm::class)
            ->set('inputs.type', "1")
            ->set('inputs.dni', "")
            ->set('inputs.ruc', "")
            ->set('inputs.name', "")
            ->set('inputs.surname_father', "")
            ->set('inputs.surname_mother', "")
            ->set('inputs.business_name', "")
            ->set('inputs.address', "")
            ->set('inputs.email', "")
            ->set('inputs.phone', "")
            ->call('updateProfileInformation')
            ->assertHasErrors(
                ['name' => 'required_if'],
                ['surname_father' => 'required_if']
            );
    }

    /** @test */
    public function validate_required_params_with_ruc(){
        $this->actingAs($this->user);

        Livewire::test(UpdateProfileInformationForm::class)
            ->set('inputs.type', "6")
            ->set('inputs.dni', "")
            ->set('inputs.ruc', "")
            ->set('inputs.name', "")
            ->set('inputs.surname_father', "")
            ->set('inputs.surname_mother', "")
            ->set('inputs.business_name', "")
            ->set('inputs.address', "")
            ->set('inputs.email', "")
            ->set('inputs.phone', "")
            ->call('updateProfileInformation')
            ->assertHasErrors(
                ['ruc' => 'required_if'],
                ['business_name' => 'required_if']
            );
    }

    /** @test */
    public function can_update_profile_information_with_ruc(){
        $this->actingAs($this->user);

        Livewire::test(UpdateProfileInformationForm::class)
            ->set('inputs.type', "6")
            ->set('inputs.dni', "")
            ->set('inputs.ruc', "10971360787")
            ->set('inputs.name', "")
            ->set('inputs.surname_father', "")
            ->set('inputs.surname_mother', "")
            ->set('inputs.business_name', "GUILLERMO ESPINOZA TORRES")
            ->set('inputs.address', "Huascar 285")
            ->set('inputs.email', "guiesto.07@hotmail.com")
            ->set('inputs.phone', "981702686")
            ->call('updateProfileInformation');

        $this->user->refresh();

        $this->assertEquals($this->user->person->type, 6);
        $this->assertEquals($this->user->person->ruc, "10971360787");
        $this->assertEquals($this->user->person->business_name, "GUILLERMO ESPINOZA TORRES");
        $this->assertEquals($this->user->person->address, "Huascar 285");
        $this->assertEquals($this->user->person->email, "guiesto.07@hotmail.com");
        $this->assertEquals($this->user->person->phone, "981702686");

        $this->assertNull($this->user->person->dni);
        $this->assertNull($this->user->person->name);
        $this->assertNull($this->user->person->surname_father);
        $this->assertNull($this->user->person->surname_mother);
    }

    /** @test */
    public function can_update_profile_information_with_dni(){
        $this->actingAs($this->user);

        Livewire::test(UpdateProfileInformationForm::class)
            ->set('inputs.type', "1")
            ->set('inputs.dni', "12345678")
            ->set('inputs.ruc', "")
            ->set('inputs.name', "GUILLERMO")
            ->set('inputs.surname_father', "ESPINOZA")
            ->set('inputs.surname_mother', "")
            ->set('inputs.business_name', "GUILLERMO ESPINOZA TORRES")
            ->set('inputs.address', "Huascar 285")
            ->set('inputs.email', "guiesto.07@hotmail.com")
            ->set('inputs.phone', "981702686")
            ->call('updateProfileInformation');

        $this->user->refresh();

        $this->assertEquals($this->user->person->type, 1);
        $this->assertEquals($this->user->person->dni, "12345678");
        $this->assertEquals($this->user->person->name, "GUILLERMO");
        $this->assertEquals($this->user->person->surname_father, "ESPINOZA");
        $this->assertEquals($this->user->person->surname_mother, "");
        $this->assertEquals($this->user->person->address, "Huascar 285");
        $this->assertEquals($this->user->person->email, "guiesto.07@hotmail.com");
        $this->assertEquals($this->user->person->phone, "981702686");

        $this->assertNull($this->user->person->ruc);
        $this->assertNull($this->user->person->business_name);

    }

    /** @test */
    public function event_success_is_emmitted_when_update(){
        $this->actingAs($this->user);

        Livewire::test(UpdateProfileInformationForm::class)
            ->set('inputs.type', "6")
            ->set('inputs.dni', "")
            ->set('inputs.ruc', "10971360787")
            ->set('inputs.name', "")
            ->set('inputs.surname_father', "")
            ->set('inputs.surname_mother', "")
            ->set('inputs.business_name', "GUILLERMO ESPINOZA TORRES")
            ->set('inputs.address', "Huascar 285")
            ->set('inputs.email', "guiesto.07@hotmail.com")
            ->set('inputs.phone', "981702686")
            ->call('updateProfileInformation')

            ->assertEmitted('alert-success')
            ->assertEmitted('update-profile');
    }

}
