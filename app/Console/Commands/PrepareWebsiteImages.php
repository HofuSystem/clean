<?php

namespace App\Console\Commands;

use App\Support\WebsiteImages;
use Core\Pages\Models\Feature;
use Core\Blog\Models\Blog;
use Core\Pages\Models\Section;
use Illuminate\Console\Command;

class PrepareWebsiteImages extends Command
{
    protected $signature = 'website:prepare-images {--path=* : Optional public storage paths to prepare}';
    protected $description = 'Create responsive website image copies without replacing originals or updating database records';

    public function handle(): int
    {
        $paths = collect($this->option('path'));
        if ($paths->isEmpty()) {
            $paths = Section::query()->whereIn('template', ['hero', 'app-features'])->pluck('images')
                ->concat(Feature::query()->where('section', 'b2c')->pluck('image'))
                ->concat(Blog::query()->published()->pluck('image'))
                ->push(config('app.logo'));
        }
        $paths = $paths->filter()->flatMap(fn ($path) => explode(',', $path))->unique();
        $ready = 0;
        foreach ($paths as $path) {
            if (WebsiteImages::prepare(trim($path))) {
                $ready++;
            } else {
                $this->warn('Skipped a missing, external, unsupported, or unwritable image. Original kept.');
            }
        }
        $this->info("Prepared {$ready} of {$paths->count()} website images. Original files and database records unchanged.");
        return $ready === $paths->count() ? self::SUCCESS : self::FAILURE;
    }
}
