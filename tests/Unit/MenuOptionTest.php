<?php

namespace Tests\Unit;

use App\Models\Action;
use App\Models\MenuCategory;
use App\Models\MenuOption;
use App\Models\Permission;
use App\Models\UserType;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class MenuOptionTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /** @test  */
    public function test_menu_options_database_has_expected_columns()
    {
        $this->assertTrue(
            Schema::hasColumns('menu_options', [
                'id',
                'name',
                'order',
                'link',
                'menu_category_id',
                'created_at',
                'updated_at',
                'deleted_at'
            ]), 1);
    }

    /** @test  */
    public function test_a_menu_option_belongs_to_a_menu_category(){
        $menu_option = MenuOption::factory()->create([
            'menu_category_id' => MenuCategory::factory()->create(),
        ]);

        $this->assertInstanceOf(MenuCategory::class, $menu_option->menu_category);
    }

    /** @test  */
    public function test_a_menu_option_has_many_actions(){
        $menu_option = MenuOption::factory()->create();

        $actions = Action::factory()->count(3)->create([
            'menu_option_id' => $menu_option->id
        ]);

        $this->assertEquals(3, count($menu_option->actions));
        $this->assertInstanceOf('Illuminate\Database\Eloquent\Collection', $menu_option->actions);
        $this->assertInstanceOf(Action::class, $menu_option->actions[0]);
    }

    /** @test  */
    public function test_a_menu_option_belongs_to_many_user_types(){
        $user_types = UserType::factory()->count(3)->create();
        $menu_option = MenuOption::factory()->create();

        foreach ($user_types as $key => $value) {
            Permission::factory()->create([
                'user_type_id' => $value->id,
                'menu_option_id' => $menu_option->id,
            ]);
        }

        $this->assertEquals(3, count($menu_option->user_types));
        $this->assertInstanceOf('Illuminate\Database\Eloquent\Collection', $menu_option->user_types);
        $this->assertInstanceOf(UserType::class, $menu_option->user_types[0]);

    }
}
