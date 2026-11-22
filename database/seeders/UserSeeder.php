<?php

namespace Database\Seeders;

use App\Models\Media;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $tugas = User::firstOrCreate(
            ['email' => 'tugas@flipplay.test'],
            [
                'name' => 'Tugas',
                'password' => Hash::make('password123'),
            ]
        );

        User::firstOrCreate(
            ['email' => 'reviewer@flipplay.test'],
            [
                'name' => 'Reviewer',
                'password' => Hash::make('password123'),
            ]
        );

        $grandBlue = Media::where('title', 'Grand Blue Dreaming')->first();
        $danmachi = Media::where('title', 'DanMachi')->first();

        $mediaIds = array_filter([
            $grandBlue?->id,
            $danmachi?->id,
        ]);

        if (! empty($mediaIds)) {
            $tugas->media()->syncWithoutDetaching($mediaIds);
        }
    }
}
