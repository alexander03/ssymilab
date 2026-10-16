<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call([
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
            CardSeeder::class,
            TransferMethodSeeder::class,
            CurrencySeeder::class,
            RoleSeeder::class,
            PersonRoleSeeder::class,
            CheckoutSeeder::class,
            ModuleSeeder::class,
            DocumentTypeSeeder::class,
            ReasonsSeeder::class,

        ]);
    }
}
