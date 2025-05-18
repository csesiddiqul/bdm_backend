<?php

namespace Database\Seeders;

use App\Models\Slider;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class SliderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $sliders = [
            [
                'url' => 'https://mediplus-html.vercel.app/img/slider2.jpg',
                'image_name' => 'slider_1.jpg',
                'title' => 'We Provide Medical Services That You Can Trust!',
                'subtitle' => 'Medical Services',
                'description' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit.',
            ],
            [
                'url' => 'https://mediplus-html.vercel.app/img/slider.jpg',
                'image_name' => 'slider_2.jpg',
                'title' => 'Your Health is Our Priority!',
                'subtitle' => 'Qualified Professionals',
                'description' => 'Curabitur aliquet quam id dui posuere blandit.',
            ],
        ];

        foreach ($sliders as $slider) {
            try {
                $response = Http::get($slider['url']);

                if ($response->ok()) {
                    Storage::disk('public')->put('slider-image/' . $slider['image_name'], contents: $response->body());

                    Slider::create([
                        'title' => $slider['title'],
                        'subtitle' => $slider['subtitle'],
                        'description' => $slider['description'],
                        'image' => 'storage/slider-image/' . $slider['image_name'], // Public path
                    ]);
                    $this->command->info("✅ Slider '{$slider['image_name']}' saved successfully.");
                } else {
                    $this->command->error("❌ Failed to download image: " . $slider['url']);
                }
            } catch (\Exception $e) {
                $this->command->error("❌ Error downloading image: " . $e->getMessage());
            }
        }
    }
}
