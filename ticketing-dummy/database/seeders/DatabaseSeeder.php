<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Event;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Seed a test user
        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        // Seed mock events
        Event::create([
            'title' => 'Coldplay Live in Jakarta 2026',
            'image_url' => 'https://images.unsplash.com/photo-1501281668745-f7f57925c3b4?w=800',
            'date_time' => Carbon::parse('2026-11-15 20:00:00'),
            'location' => 'Gelora Bung Karno Stadium, Jakarta',
            'price' => 1500000,
            'quota' => 100,
        ]);

        Event::create([
            'title' => 'Indonesia Web Summit 2026',
            'image_url' => 'https://images.unsplash.com/photo-1540575467063-178a50c2df87?w=800',
            'date_time' => Carbon::parse('2026-08-20 09:00:00'),
            'location' => 'Jakarta Convention Center (JCC)',
            'price' => 250000,
            'quota' => 500,
        ]);

        Event::create([
            'title' => 'UI/UX Design Masterclass Workshop',
            'image_url' => 'https://images.unsplash.com/photo-1515187029135-18ee286d815b?w=800',
            'date_time' => Carbon::parse('2026-09-05 13:00:00'),
            'location' => 'Co-working Space, BSD City',
            'price' => 100000,
            'quota' => 30,
        ]);
    }
}
