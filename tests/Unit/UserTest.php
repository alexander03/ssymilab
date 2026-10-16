<?php

namespace Tests\Unit;

use App\Models\BranchOffice;
use App\Models\MenuOption;
use App\Models\Person;
use App\Models\User;
use App\Models\UserType;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /** @test  */
    public function test_users_database_has_expected_columns()
    {
        $this->assertTrue(
            Schema::hasColumns('users', [
                'id',
                'username',
                'password',
                'email',
                'email_verified_at',
                'status',
                'branch_office_id',
                'person_id',
                'user_type_id',
                'created_at',
                'updated_at',
                'deleted_at'
            ]), 1);
    }

    /** @test  */
    public function test_a_user_belongs_to_a_person(){
        $person = Person::factory()->create();
        $user =  User::factory()->create(['person_id' => $person->id]);

        $this->assertInstanceOf(Person::class, $user->person);
    }

    /** @test  */
    public function test_a_user_belongs_to_a_branch_office(){
        $brach_office = BranchOffice::factory()->create();
        $user =  User::factory()->create(['branch_office_id' => $brach_office->id]);

        $this->assertInstanceOf(BranchOffice::class, $user->branch_office);
    }

    /** @test  */
    public function test_a_user_belongs_to_a_user_type(){
        $user_type = UserType::factory()->create();
        $user =  User::factory()->create(['user_type_id' => $user_type->id]);

        $this->assertInstanceOf(UserType::class, $user->user_type);
    }

    /** @test  */
    public function test_user_can_get_permissions_for_sidebar(){

        $menu_options = User::find(1)->get_menu_options()->get();

        $this->assertInstanceOf('Illuminate\Database\Eloquent\Collection', $menu_options);
        $this->assertInstanceOf(MenuOption::class, $menu_options[0]);
    }

}
