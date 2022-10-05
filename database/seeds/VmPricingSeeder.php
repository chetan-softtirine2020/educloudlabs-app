<?php

use App\Models\PricingChart;
use Illuminate\Database\Seeder;

class VmPricingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $price1 = new PricingChart();
        $price1->memory = 2;
        $price1->virtual_cpu = 2;
        $price1->linux = 0.03;
        $price1->windows = 0.07;
        $price1->storage_price = 0.170;
        $price1->save();

        $price2 = new PricingChart();
        $price2->memory = 4;
        $price2->virtual_cpu = 2;
        $price2->linux = 0.05;
        $price2->windows = 0.2;
        $price2->save();


        $price3 = new PricingChart();
        $price3->memory = 8;
        $price3->virtual_cpu = 2;
        $price3->linux = 0.1;
        $price3->windows = 0.3;
        $price3->save();

        $price4 = new PricingChart();
        $price4->memory = 16;
        $price4->virtual_cpu = 4;
        $price4->linux = 0.2;
        $price4->windows = 0.6;
        $price4->save();

        $price5 = new PricingChart();
        $price5->memory = 32;
        $price5->virtual_cpu = 8;
        $price5->linux = 0.42;
        $price5->windows = 1.4;
        $price5->save();
    }
}
