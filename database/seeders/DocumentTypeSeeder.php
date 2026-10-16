<?php

namespace Database\Seeders;

use App\Models\DocumentType;
use Illuminate\Database\Seeder;

class DocumentTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DocumentType::create([
            'type' => 'N',
            'name' => 'REQUERIMIENTOS',
            'code' => 'REQ',
            'module_id' => 1
        ]);
    }
}
