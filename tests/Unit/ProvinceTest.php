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

class ProvinceTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /** @test  */
    public function test_provinces_database_has_expected_columns()
    {
        $this->assertTrue(
            Schema::hasColumns('provinces', [
                'code',
                'name',
                'department_id',
                'created_at',
                'updated_at',
                'deleted_at'
            ]), 1);
    }

    /** @test */
    public function test_a_province_has_many_districts(){

        $province = Province::find('1401');

        $this->assertInstanceOf(Collection::class, $province->districts);
        $this->assertInstanceOf(District::class, $province->districts[0]);
    }

    /** @test */
    public function test_a_province_belongs_to_a_department(){

        $province = Province::find('1401');

        $this->assertInstanceOf(Department::class, $province->department);
    }
}
