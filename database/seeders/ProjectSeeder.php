<?php

namespace Database\Seeders;

use App\Models\Project;
use Illuminate\Database\Seeder;

class ProjectSeeder extends Seeder
{
    public function run(): void
    {
        $projects = [
            ['name' => 'JRR', 'sort_order' => 1, 'is_active' => true],
            ['name' => 'Primas', 'sort_order' => 2, 'is_active' => true],
            ['name' => 'Project A', 'sort_order' => 10, 'is_active' => true],
            ['name' => 'Project B', 'sort_order' => 11, 'is_active' => true],
            ['name' => 'Project C', 'sort_order' => 12, 'is_active' => true],
            ['name' => 'Project D', 'sort_order' => 13, 'is_active' => true],
            ['name' => 'Project E', 'sort_order' => 14, 'is_active' => true],
            ['name' => 'Project F', 'sort_order' => 15, 'is_active' => true],
            ['name' => 'Project G', 'sort_order' => 16, 'is_active' => true],
            ['name' => 'Project H', 'sort_order' => 17, 'is_active' => true],
        ];

        foreach ($projects as $project) {
            Project::updateOrCreate(
                ['name' => $project['name']],
                $project
            );
        }
    }
}
