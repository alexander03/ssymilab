<?php

namespace Tests\Unit;

use App\Models\BranchOffice;
use App\Models\Company;
use App\Models\Department;
use App\Models\District;
use App\Models\Province;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class BranchOfficeTest extends TestCase
{
    use RefreshDatabase, WithFaker;
    /** @test  */
    public function test_branch_offices_database_has_expected_columns()
    {
        $this->assertTrue(
            Schema::hasColumns('branch_offices', [
                'id',
                'name',
                'address',
                'phone',
                'company_id',
                'department_id',
                'province_id',
                'district_id',
                'created_at',
                'updated_at',
                'deleted_at'
            ]), 1);
    }

    /** @test */
    public function test_a_branch_office_belongs_to_a_department(){
        $branch_office = BranchOffice::factory()->create([
            'department_id' => '15'
        ]);

        $this->assertInstanceOf(Department::class, $branch_office->department);
        $this->assertEquals('15', $branch_office->department->code);
    }

    /** @test */
    public function test_a_branch_office_belongs_to_a_province(){
        $branch_office = BranchOffice::factory()->create([
            'province_id' => '1501'
        ]);

        $this->assertInstanceOf(Province::class, $branch_office->province);
        $this->assertEquals('1501', $branch_office->province->code);
    }

    /** @test */
    public function test_a_branch_office_belongs_to_a_district(){
        $branch_office = BranchOffice::factory()->create([
            'district_id' => '150101'
        ]);

        $this->assertInstanceOf(District::class, $branch_office->district);
        $this->assertEquals('150101', $branch_office->district->code);
    }

    /** @test */
    public function test_a_branch_office_belongs_to_a_company(){
        $branch_office = BranchOffice::factory()->create([
            'company_id' => 1
        ]);

        $this->assertInstanceOf(Company::class, $branch_office->company);
        $this->assertEquals(1, $branch_office->company->id);
    }

    /** @test */
    public function test_a_branch_office_has_many_users(){
        $branch_office = BranchOffice::factory()->create();
        $users = User::factory()->count(3)->create([
            'branch_office_id' => $branch_office->id,
        ]);

        $this->assertInstanceOf(Collection::class, $branch_office->users);
        $this->assertEquals(3, count($branch_office->users));
        $this->assertInstanceOf(User::class, $branch_office->users[0]);
    }
}
