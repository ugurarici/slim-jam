<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\DTO\Product;
use App\Helpers\TranslateHelper as TranslateClient;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Illuminate\Support\Facades\Storage;
use App\Jobs\CreateProductOnShopifyJob;

class CreateShopifyProductsFromExcelJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(TranslateClient $translater)
    {
        //  Get Excel file
        $spreadsheet = IOFactory::load(Storage::path('demo.xls'));
        $productsData = Product::createCollectionFromExcel($spreadsheet);

        //  Create a Shopify product with each line
        foreach ($productsData as $productData) {

            $productToCreate = [
                "title" =>
                $translater->translate($productData->type) . " " .
                    $productData->collection . ", " .
                    $translater->translate($productData->color) . ", " .
                    $productData->sizeName,

                "body_html" => "<strong>" . $translater->translate(
                    $productData->type . " " . $productData->category
                ) . "!</strong>",

                "vendor" => $productData->brand,

                "product_type" => $translater->translate(
                    $productData->type
                ),

                "variants" => [
                    [
                        "sku" => $productData->code,
                        "price" => $productData->price,
                    ]
                ],

                "images" => $productData->images,
            ];

            $dispatchedJob = CreateProductOnShopifyJob::dispatch($productToCreate);
        }
    }
}
