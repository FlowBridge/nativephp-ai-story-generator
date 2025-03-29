<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\StoryElement;
use App\Models\StoryElementType;

class StoryElementTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $animal = StoryElementType::firstOrCreate(
            ['name' => 'Animal'],
            ['category' => 'Animal']
        );

        $emotion = StoryElementType::firstOrCreate(
            ['name' => 'Emotion'],
            ['category' => 'Emotion']
        );


        $animals = [
            ['name' => 'Sheep', 'description' => 'N/a', 'type_id' => $animal->id],
            ['name' => 'Turtle', 'description' => 'N/a', 'type_id' => $animal->id],
        ];

        foreach ($animals as $animal) {
            StoryElement::firstOrCreate([
                'name' => $animal['name'],
                'description' => $animal['description'],
                'type_id' => $animal['type_id']
            ]);
        }

        $emotions = [
            ['name' => 'Happy', 'description' => 'N/a', 'type_id' => $emotion->id],
            ['name' => 'Sad', 'description' => 'N/a', 'type_id' => $emotion->id],

        ];

        foreach ($emotions as $emotion) {
            StoryElement::firstOrCreate([
                'name' => $emotion['name'],
                'description' => $emotion['description'],
                'type_id' => $emotion['type_id']
            ]);
        }
    }
}
