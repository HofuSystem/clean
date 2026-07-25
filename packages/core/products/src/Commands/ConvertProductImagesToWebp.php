<?php

namespace Core\Products\Commands;

use Illuminate\Console\Command;
use Core\Products\Services\ProductsService;

class ConvertProductImagesToWebp extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'products:convert-webp {--quality=60 : Quality for sales products compression}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Convert product images to WebP format, and compress sales products images';

    /**
     * Execute the console command.
     */
    public function handle(ProductsService $productsService)
    {
        $this->info('Starting conversion of product images to WebP...');

        $quality = (int) $this->option('quality');
        $result = $productsService->convertProductImagesToWebp($quality);

        $this->info("Total products evaluated: {$result['total']}");
        $this->info("Successfully converted/compressed: {$result['converted']}");

        if ($result['failed'] > 0) {
            $this->warn("Skipped/Failed: {$result['failed']}");
            foreach ($result['errors'] as $error) {
                $this->error($error);
            }
        }

        $this->info('Product images conversion completed.');
        return Command::SUCCESS;
    }
}
