<?php

use Illuminate\Database\Seeder;
use Faker\Factory as Faker;
class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker::create();
        $statuses = ['Iphone', 'Approved', 'Sam Sung', 'Nokia', 'Xiaomi', 'Oppo'];
        foreach (range(1, 400) as $index) {
            App\Product::insert([
                'name' => $statuses[shuffle($statuses)],
                'description' => $faker->dateTimeBetween('+1 month', '+2 month'),
                'image' => 'https://www.dungplus.com/wp-content/uploads/2019/12/girl-xinh-600x600.jpg',
            ]);
        }
    }
}
