<?php

namespace Tests\Unit;

use App\Models\Department;
use App\Models\District;
use App\Models\Province;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class DepartmentTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /** @test  */
    public function test_departments_database_has_expected_columns()
    {
        $this->assertTrue(
            Schema::hasColumns('departments', [
                'code',
                'name',
                'created_at',
                'updated_at',
                'deleted_at'
            ]), 1);
    }

    /** @test */
    public function test_a_department_has_many_provinces(){

        $department = Department::find('14');

        $this->assertInstanceOf(Collection::class, $department->provinces);
        $this->assertInstanceOf(Province::class, $department->provinces[0]);
    }

    /** @test */
    public function test_a_department_has_many_districts(){

        $department = Department::find('14');

        $this->assertInstanceOf(Collection::class, $department->districts);
        $this->assertInstanceOf(District::class, $department->districts[0]);
    }
}
