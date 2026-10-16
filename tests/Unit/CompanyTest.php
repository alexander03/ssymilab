<?php

namespace Tests\Unit;

use App\Models\BranchOffice;
use App\Models\Company;
use App\Models\Department;
use App\Models\District;
use App\Models\Province;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class CompanyTest extends TestCase
{
    use RefreshDatabase, WithFaker;
    /** @test  */
    public function test_companies_database_has_expected_columns()
    {
        $this->assertTrue(
            Schema::hasColumns('companies', [
                'id',
                'ruc',
                'business_name',
                'trade_name',
                'address',
                'password',
                'authentication_mode',
                'username_sunat',
                'password_sunat',
                'department_id',
                'province_id',
                'district_id',
                'status',
                'created_at',
                'updated_at',
                'deleted_at'
            ]), 1);
    }

    /** @test */
    public function test_a_company_belongs_to_a_department(){
        $company = Company::find(1);

        $this->assertInstanceOf(Department::class, $company->department);
        $this->assertEquals('14', $company->department->code);
    }

    /** @test */
    public function test_a_company_belongs_to_a_province(){
        $company = Company::find(1);

        $this->assertInstanceOf(Province::class, $company->province);
        $this->assertEquals('1401', $company->province->code);
    }

    /** @test */
    public function test_a_company_belongs_to_a_district(){
        $company = Company::find(1);

        $this->assertInstanceOf(District::class, $company->district);
        $this->assertEquals('140101', $company->district->code);
    }

    /** @test */
    public function test_a_company_has_many_branch_offices(){
        $company = Company::find(1);
        $branch_offices = BranchOffice::factory()->count(3)->create([
            'company_id' => $company->id,
        ]);

        $this->assertInstanceOf(Collection::class, $company->branch_offices);
        $this->assertEquals(4, count($company->branch_offices));
        $this->assertInstanceOf(BranchOffice::class, $company->branch_offices[0]);
    }
}
