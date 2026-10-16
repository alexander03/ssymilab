<?php

namespace Tests\Feature\Http\Controllers\Users;

use App\Http\Livewire\Users\Profile\UpdateProfileInformationForm;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ProfileControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function setUp() : void
    {
        parent::setUp();

        /** @var \Illuminate\Contracts\Auth\Authenticatable $user */
        $this->user = User::find(1);
    }

    /** @test */
    public function can_see_profile_view()
    {
        $this->actingAs($this->user);

        $response = $this->get(route('profile'));

        $response->assertSuccessful();
        $response->assertViewIs('app.users.profile.admin');
    }

    /** @test */
    public function profile_page_contains_livewire_component(){

        $this->actingAs($this->user);

        $response = $this->get(route('profile'));

        $response->assertSeeLivewire(UpdateProfileInformationForm::class);

    }
}
