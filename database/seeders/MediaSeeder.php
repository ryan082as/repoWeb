<?php

namespace Database\Seeders;

use App\Models\Media;
use Illuminate\Database\Seeder;

class MediaSeeder extends Seeder
{
    public function run(): void
    {
        $items = [
            [
                'title' => 'Grand Blue Dreaming',
                'type' => 'Anime',
                'genre' => 'Comedy / Slice of Life',
                'rating' => 8.4,
                'release_year' => 2018,
                'total_count' => 12,
                'creator' => 'Zero-G',
            ],
            [
                'title' => 'Attack on Titan',
                'type' => 'Anime',
                'genre' => 'Action / Dark Fantasy',
                'rating' => 8.5,
                'release_year' => 2013,
                'total_count' => 25,
                'creator' => 'Wit Studio',
            ],
            [
                'title' => 'Sousou no Frieren',
                'type' => 'Anime',
                'genre' => 'Adventure / Fantasy',
                'rating' => 9.3,
                'release_year' => 2023,
                'total_count' => 28,
                'creator' => 'Madhouse',
            ],
            [
                'title' => 'DanMachi',
                'type' => 'Manga',
                'genre' => 'Action / Fantasy',
                'rating' => 7.6,
                'release_year' => 2013,
                'total_count' => 90,
                'creator' => 'Fujino Oomori',
            ],
            [
                'title' => 'One Punch Man',
                'type' => 'Manga',
                'genre' => 'Action / Superhero',
                'rating' => 8.7,
                'release_year' => 2012,
                'total_count' => 200,
                'creator' => 'ONE & Yusuke Murata',
            ],
        ];

        foreach ($items as $item) {
            Media::firstOrCreate(['title' => $item['title']], $item);
        }
    }
}
