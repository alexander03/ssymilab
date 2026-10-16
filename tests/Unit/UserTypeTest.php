<?php

namespace Tests\Unit;

use App\Models\Action;
use App\Models\MenuOption;
use App\Models\Operation;
use App\Models\Permission;
use App\Models\User;
use App\Models\UserType;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class UserTypeTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /** @test  */
    public function test_user_types_database_has_expected_columns()
    {
        $this->assertTrue(
            Schema::hasColumns('user_types', [
                'id',
                'name',
                'code',
                'created_at',
                'updated_at',
                'deleted_at'
            ]), 1);
    }

    /** @test */
    public function test_a_user_type_has_many_users(){

        $user_type = UserType::factory()->create();
        $users = User::factory()->count(3)->create([
            'user_type_id' => $user_type->id,
        ]);

        $this->assertInstanceOf(Collection::class, $user_type->users);
        $this->assertInstanceOf(User::class, $user_type->users[0]);
    }

    /** @test */
    public function test_a_user_type_belongs_to_many_menu_options(){
        $menu_options = MenuOption::factory()->count(3)->create();
        $user_type = UserType::factory()->create();

        foreach ($menu_options as $key => $value) {
            Permission::factory()->create([
                'user_type_id' => $user_type->id,
                'menu_option_id' => $value->id
            ]);
        }

        $this->assertInstanceOf(Collection::class, $user_type->menu_options);
        $this->assertEquals(3, count($user_type->menu_options));
        $this->assertInstanceOf(MenuOption::class, $user_type->menu_options[0]);
    }

    /** @test */
    public function test_a_user_type_belongs_to_many_actions(){
        $actions = Action::factory()->count(3)->create();
        $user_type = UserType::factory()->create();

        foreach ($actions as $key => $value) {
            Operation::factory()->create([
                'user_type_id' => $user_type->id,
                'action_id' => $value->id
            ]);
        }

        $this->assertInstanceOf(Collection::class, $user_type->actions);
        $this->assertEquals(3, count($user_type->actions));
        $this->assertInstanceOf(Action::class, $user_type->actions[0]);
    }

    /** @test */
    public function test_can_get_actions_by_menu_option(){
        $menu_option = MenuOption::factory()->create();
        $menu_option_2 = MenuOption::factory()->create();
        $user_type = UserType::factory()->create();
        $actions = Action::factory()->count(5)->create([
            'menu_option_id' => $menu_option->id,
        ]);
        $actions_2 = Action::factory()->count(5)->create([
            'menu_option_id' => $menu_option_2->id,
        ]);

        foreach ($actions as $key => $value) {
            Operation::factory()->create([
                'user_type_id' => $user_type->id,
                'action_id' => $value->id
            ]);
        }

        foreach ($actions_2 as $key => $value) {
            Operation::factory()->create([
                'user_type_id' => $user_type->id,
                'action_id' => $value->id
            ]);
        }

        $actions_filtered = $user_type->actions_by_menu_option($menu_option->id)->get();

        $this->assertInstanceOf(Collection::class, $actions_filtered);
        $this->assertEquals(5, count($actions_filtered));
        $this->assertInstanceOf(Action::class, $actions_filtered[0]);
        $this->assertEquals($menu_option->id, $actions_filtered[0]->menu_option_id);
    }
}
