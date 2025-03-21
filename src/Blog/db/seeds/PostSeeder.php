<?php

declare(strict_types=1);

use Faker\Factory;
use Phinx\Seed\AbstractSeed;

class PostSeeder extends AbstractSeed
{
    public function run(): void
    {
        // Categories
        $faker = Factory::create('fr_FR');
        $data = [];
        for ($i = 0; $i < 5; $i++) {
            $date = $faker->unixTime('now');
            $data[] = [
                'name' => $faker->country(),
                'slug' => $faker->slug()
            ];
        }
        $this->table('categories')
            ->insert($data)
            ->save();

        // Posts
        $data = [];
        for ($i = 0; $i < 100; $i++) {
            $date = $faker->unixTime('now');
            $data[] = [
                'name' => $faker->word(),
                'slug' => $faker->slug(),
                'content' => $faker->text(3000),
                'created_at' => $faker->date('Y-m-d H:i:s', $date),
                'updated_at' => $faker->date('Y-m-d H:i:s', $date),
                'category_id' => rand(1, 5)
            ];
        }
        $this->table('posts')
            ->insert($data)
            ->save();
    }
}
