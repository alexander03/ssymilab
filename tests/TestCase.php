<?php

namespace Tests;

use Database\Seeders\ActionSeeder;
use Database\Seeders\BranchOfficeSeeder;
use Database\Seeders\CompanySeeder;
use Database\Seeders\DepartmentSeeder;
use Database\Seeders\DistrictSeeder;
use Database\Seeders\MenuCategorySeeder;
use Database\Seeders\MenuOptionSeeder;
use Database\Seeders\OperationSeeder;
use Database\Seeders\PermissionSeeder;
use Database\Seeders\PersonSeeder;
use Database\Seeders\ProvinceSeeder;
use Database\Seeders\UserSeeder;
use Database\Seeders\UserTypeSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication, RefreshDatabase, SeedDatabase;

    public function setUp(): void
    {
        parent::setUp();
        SeedDatabaseState::$seeders = [
            DepartmentSeeder::class,
            ProvinceSeeder::class,
            DistrictSeeder::class,
            CompanySeeder::class,
            BranchOfficeSeeder::class,
            UserTypeSeeder::class,
            PersonSeeder::class,
            UserSeeder::class,
            MenuCategorySeeder::class,
            MenuOptionSeeder::class,
            ActionSeeder::class,
            PermissionSeeder::class,
            OperationSeeder::class,
        ];
        $this->seedDatabase();
    }
}
