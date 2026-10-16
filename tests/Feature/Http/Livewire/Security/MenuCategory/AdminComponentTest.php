<?php

namespace Tests\Feature\Http\Livewire\Security\MenuCategory;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Livewire\Livewire;
use Tests\TestCase;

class AdminComponentTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function setUp() : void
    {
        parent::setUp();

        /** @var \Illuminate\Contracts\Auth\Authenticatable $user */
        $this->user = User::find(1);
        $this->livewire_component = 'security.menu-category.admin-component';
    }

    /** @test */
    public function can_see_admin_view(){

        $this->actingAs($this->user);

        Livewire::test($this->livewire_component)
            ->assertViewIs('livewire.security.menu-category.admin-component');

    }
}
