<?php

declare(strict_types=1);

use Faker\Factory;
use Phinx\Seed\AbstractSeed;

class PostSeeder extends AbstractSeed
{
    public function run(): void
    {
        $faker = Factory::create('fr_FR');
        $data = [];
        for ($i = 0; $i < 100; $i++) {
            $date = $faker->unixTime('now');
            $data[] = [
                'name' => $faker->word(),
                'slug' => $faker->slug(),
                'content' => $faker->text(3000),
                'created_at' => $faker->date('Y-m-d H:i:s', $date),
                'updated_at' => $faker->date('Y-m-d H:i:s', $date)
            ];
        }
        $this->table('posts')
            ->insert($data)
            ->save();
    }
}
