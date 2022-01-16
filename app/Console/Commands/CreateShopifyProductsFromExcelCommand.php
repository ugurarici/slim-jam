<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Google\Cloud\Translate\V2\TranslateClient;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Illuminate\Support\Facades\Storage;
use App\Jobs\CreateProductOnShopifyJob;

class CreateShopifyProductsFromExcelCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'shopify:fromexcel';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create Shopify Products From Excel File';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(TranslateClient $translater)
    {
        //  Get Excel file
        $spreadsheet = IOFactory::load(Storage::path('demo.xls'));
        $worksheet = $spreadsheet->getActiveSheet();
        $highestRow = $worksheet->getHighestRow();

        $this->line("Excel has " . $highestRow . " lines. It means it has " . ((int)$highestRow - 3) . " products.");

        $imageUrlColumns = ["AZ", "BA", "BB", "BC", "BD", "BE", "BF", "BG", "BH", "BJ"];

        $productsData = [];

        for ($i = 4; $i <= $highestRow; $i++) {
            $productData = [
                "code" => $worksheet->getCell("E" . $i)->getValue(),
                "brand" => $worksheet->getCell("F" . $i)->getValue(),
                "type" => $worksheet->getCell("I" . $i)->getValue(),
                "width" => $worksheet->getCell("Y" . $i)->getValue(),
                "height" => $worksheet->getCell("Z" . $i)->getValue(),
                "length" => $worksheet->getCell("X" . $i)->getValue(),
                "color" => explode("\n", $worksheet->getCell('AL' . $i)->getValue())[0],
                "collection" => $worksheet->getCell("K" . $i)->getValue(),
                "category" => $worksheet->getCell("J" . $i)->getValue(),
                "price" => $worksheet->getCell("Q" . $i)->getValue(),
                "images" => [],
            ];

            foreach ($imageUrlColumns as $column) {
                if ($worksheet->getCell($column . $i)->getValue() != "") {
                    $productData["images"][] = ["src" => $worksheet->getCell($column . $i)->getValue()];
                }
            }

            $sizeParts = [];
            foreach (["width", "height", "length"] as $column) {
                if ($productData[$column] != "") {
                    $sizeParts[] = $productData[$column];
                }
            }
            $sizeBaseName = implode('x', $sizeParts);
            $productData["sizeName"] = "";
            if ($sizeBaseName != "") $productData["sizeName"] = $sizeBaseName . " cm";

            $productsData[] = $productData;
        }

        //  Create a Shopify product with each line
        foreach ($productsData as $productData) {

            $productToCreate = [
                "title" =>
                $translater->translate($productData["type"])['text'] . " " .
                    $productData["collection"] . ", " .
                    $translater->translate(
                        $productData["color"]
                    )['text'] . ", "
                    . $productData["sizeName"],

                "body_html" => "<strong>" . $translater->translate(
                    $productData["type"] . " " . $productData["category"]
                )['text'] . "!</strong>",

                "vendor" => $productData["brand"],

                "product_type" => $translater->translate(
                    $productData["type"]
                )['text'],

                "variants" => [
                    [
                        "sku" => $productData["code"],
                        "price" => $productData["price"],
                    ]
                ],

                "images" => $productData["images"],
            ];

            $this->line($productToCreate["title"] . " creating with " . count($productToCreate["images"]) . " images...");

            $dispatchedJob = CreateProductOnShopifyJob::dispatch($productToCreate);

            $this->info("Create product job has been dipatched.");
            $this->line(" ");
        }

        $this->info("Finished");
    }
}
