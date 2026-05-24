<?php

namespace Tests\Feature;

use App\Support\ImageOptimizer;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ImageOptimizerTest extends TestCase
{
    public function test_it_optimizes_uploaded_images_before_storing(): void
    {
        Storage::fake('public');

        $image = UploadedFile::fake()->image('produto.jpg', 2200, 1400);

        $path = ImageOptimizer::store($image, 'products', 1600, 1600);

        Storage::disk('public')->assertExists($path);

        $this->assertStringStartsWith('products/', $path);
        $this->assertStringEndsWith('.webp', $path);
    }
}
