<?php

namespace Tests\Feature\Http\Controllers\Security;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class MenuCategoryControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function setUp() : void
    {
        parent::setUp();

        /** @var \Illuminate\Contracts\Auth\Authenticatable $user */
        $this->user = User::find(1);
    }

    /** @test */
    public function can_see_menu_category_view()
    {
        $this->actingAs($this->user);

        $response = $this->get(route('menu-category'));

        $response->assertSuccessful();
        $response->assertViewIs('app.security.menu-category.admin');
    }

    /** @test */
    public function can_see_create_menu_category_view()
    {
        $this->actingAs($this->user);

        $response = $this->get(route('create-menu-category'));

        $response->assertSuccessful();
        $response->assertViewIs('app.security.menu-category.create');
    }

    /** @test */
    public function can_see_edit_menu_category_view()
    {
        $this->actingAs($this->user);

        $response = $this->get(route('edit-menu-category', ['menu_category' => 1]));

        $response->assertSuccessful();
        $response->assertViewIs('app.security.menu-category.edit');
    }

    /** @test */
    public function menu_category_page_contains_livewire_component(){

        $this->actingAs($this->user);

        $response = $this->get(route('menu-category'));

        $response->assertSeeLivewire('security.menu-category.admin-component');

    }

    /** @test */
    public function menu_category_create_page_contains_livewire_component(){

        $this->actingAs($this->user);

        $response = $this->get(route('create-menu-category'));

        $response->assertSeeLivewire('security.menu-category.mant-component');

    }

    /** @test */
    public function menu_category_edit_page_contains_livewire_component(){

        $this->actingAs($this->user);

        $response = $this->get(route('edit-menu-category', ['menu_category' => 1]));

        $response->assertSeeLivewire('security.menu-category.mant-component');

    }
}
