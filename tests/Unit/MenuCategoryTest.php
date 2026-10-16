<?php

namespace Tests\Unit;

use App\Models\MenuCategory;
use App\Models\MenuOption;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class MenuCategoryTest extends TestCase
{
    use RefreshDatabase, WithFaker;
    /** @test  */
    public function test_menu_categories_database_has_expected_columns()
    {
        $this->assertTrue(
            Schema::hasColumns('menu_categories', [
                'id',
                'name',
                'order',
                'icon',
                'created_at',
                'updated_at',
                'deleted_at'
            ]), 1);
    }

    /** @test */
    public function test_a_menu_category_has_many_menu_options(){
        $menu_category = MenuCategory::factory()->create();
        $menu_options = MenuOption::factory()->count(3)->create([
            'menu_category_id' => $menu_category->id,
        ]);

        $this->assertInstanceOf(Collection::class, $menu_category->menu_options);
        $this->assertEquals(3, count($menu_category->menu_options));
        $this->assertInstanceOf(MenuOption::class, $menu_category->menu_options[0]);
    }
}
