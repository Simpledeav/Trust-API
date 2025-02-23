<?php

namespace Database\Seeders;

use App\Models\Article;
use Faker\Factory as Faker;
use Illuminate\Support\Arr;
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
        $categories = ['business', 'investing', 'savings', 'retirement', 'management', 'trends', 'technology', 'news'];

        foreach (range(1, 20) as $index) {
            Article::create([
                'id' => (string) \Illuminate\Support\Str::uuid(),
                'title'   => $title = $faker->sentence(6, true),
                'category' => Arr::random($categories),
                'slug'    => Str::slug($title),
                'content' => $faker->paragraphs(5, true),
                'image'   => 'https://www.livemint.com/lm-img/img/2025/02/13/600x338/YEAR-END-STOCKS-0_1704108179271_1739468698886.JPG'
            ]);
        }
    }
}
