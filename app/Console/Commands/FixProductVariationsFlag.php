<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Product;

class FixProductVariationsFlag extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'products:fix-variations-flag';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Set has_variations flag to true for products that have variations';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Checking products with variations...');

        // Find all products that have variations but has_variations flag is false
        $products = Product::whereHas('variations')
            ->where('has_variations', false)
            ->get();

        if ($products->isEmpty()) {
            $this->info('No products found that need fixing.');
            return 0;
        }

        $this->info("Found {$products->count()} products that need fixing.");

        $bar = $this->output->createProgressBar($products->count());
        $bar->start();

        $fixed = 0;
        foreach ($products as $product) {
            $variationsCount = $product->variations()->count();
            $activeVariationsCount = $product->activeVariations()->count();

            // Update the flag
            $product->update(['has_variations' => true]);

            $this->newLine();
            $this->line("✓ Fixed product #{$product->id}: {$product->name} ({$variationsCount} variations, {$activeVariationsCount} active)");

            $fixed++;
            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);
        $this->info("Successfully fixed {$fixed} products!");

        return 0;
    }
}
