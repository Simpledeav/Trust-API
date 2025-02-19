<?php

namespace Database\Seeders;

use App\Models\Article;
use Faker\Factory as Faker;
use Illuminate\Support\Str;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class ArticleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create();

        foreach (range(1, 20) as $index) {
            Article::create([
                'id' => (string) \Illuminate\Support\Str::uuid(),
                'title'   => $title = $faker->sentence(6, true),
                'slug'    => Str::slug($title),
                'content' => $faker->paragraphs(5, true),
                'image'   => 'https://source.unsplash.com/random/800x600?sig=' . $index, // Placeholder images
            ]);
        }
    }
}
