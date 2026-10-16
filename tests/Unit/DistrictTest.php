<?php

namespace Tests\Unit;

use App\Models\Department;
use App\Models\District;
use App\Models\Province;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class DistrictTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /** @test  */
    public function test_districts_database_has_expected_columns()
    {
        $this->assertTrue(
            Schema::hasColumns('districts', [
                'code',
                'name',
                'department_id',
                'province_id',
                'created_at',
                'updated_at',
                'deleted_at'
            ]), 1);
    }

    /** @test */
    public function test_a_district_belongs_to_a_department(){

        $district = District::find('140101');

        $this->assertInstanceOf(Department::class, $district->department);
    }

    /** @test */
    public function test_a_district_belongs_to_a_province(){

        $district = District::find('140101');

        $this->assertInstanceOf(Province::class, $district->province);
    }
}
