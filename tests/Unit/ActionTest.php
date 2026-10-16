<?php

namespace Tests\Unit;

use App\Models\Action;
use App\Models\MenuOption;
use App\Models\Operation;
use App\Models\UserType;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class ActionTest extends TestCase
{
    use RefreshDatabase, WithFaker;
    /** @test  */
    public function test_actions_database_has_expected_columns()
    {
        $this->assertTrue(
            Schema::hasColumns('actions', [
                'id',
                'code',
                'name',
                'description',
                'route',
                'menu_option_id',
                'created_at',
                'updated_at',
                'deleted_at'
            ]), 1);
    }

    /** @test */
    public function test_a_action_belongs_to_a_menu_option(){
        $menu_option = MenuOption::factory()->create();

        $action = Action::factory()->create([
            'menu_option_id' => $menu_option->id
        ]);

        $this->assertInstanceOf(MenuOption::class, $action->menu_option);
    }

    /** @test */
    public function test_a_action_belongs_to_many_user_types(){
        $user_types = UserType::factory()->count(3)->create();
        $action = Action::factory()->create();

        foreach ($user_types as $key => $value) {
            Operation::factory()->create([
                'user_type_id' => $value->id,
                'action_id' => $action->id,
            ]);
        }

        $this->assertInstanceOf(Collection::class, $action->user_types);
        $this->assertEquals(3, count($action->user_types));
        $this->assertInstanceOf(UserType::class, $action->user_types[0]);

    }


}
