<?php

namespace Core\Admin\Database\Seeders;

use Core\Admin\Models\FixedCost;
use Illuminate\Database\Seeder;

class FixedCostSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $fixedCosts = [
            [
                'name' => 'Office Rent',
                'description' => 'Monthly office space rental',
                'amount' => 5000.00,
                'frequency' => 'monthly',
                'date' => '2024-01-01',
            ],
            [
                'name' => 'Internet & Phone',
                'description' => 'Monthly internet and phone services',
                'amount' => 300.00,
                'frequency' => 'monthly',
                'date' => '2024-01-01',
            ],
            [
                'name' => 'Electricity',
                'description' => 'Monthly electricity bills',
                'amount' => 800.00,
                'frequency' => 'monthly',
                'date' => '2024-01-01',
            ],
            [
                'name' => 'Insurance',
                'description' => 'Annual business insurance',
                'amount' => 12000.00,
                'frequency' => 'yearly',
                'date' => '2024-01-01',
            ],
            [
                'name' => 'Software Licenses',
                'description' => 'Quarterly software subscription fees',
                'amount' => 1500.00,
                'frequency' => 'quarterly',
                'date' => '2024-01-01',
            ],
            [
                'name' => 'Maintenance',
                'description' => 'Monthly equipment maintenance',
                'amount' => 400.00,
                'frequency' => 'monthly',
                'date' => '2024-01-01',
            ],
        ];

        foreach ($fixedCosts as $fixedCost) {
            FixedCost::create($fixedCost);
        }
    }
}
