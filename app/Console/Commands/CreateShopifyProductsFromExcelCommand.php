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
        //  Create a Shopify product with each line
        $spreadsheet = IOFactory::load(Storage::path('demo.xls'));
        $worksheet = $spreadsheet->getActiveSheet();
        $highestRow = $worksheet->getHighestRow();

        $this->line("Excel has " . $highestRow . " lines. It means it has " . ((int)$highestRow - 3) . " products.");

        $imageUrlColumns = ["AZ", "BA", "BB", "BC", "BD", "BE", "BF", "BG", "BH", "BJ"];

        for ($i = 4; $i <= $highestRow; $i++) {
            $sizeParts = [];
            foreach (["Y", "Z", "X"] as $column) {
                if ($worksheet->getCell($column . $i)->getValue() != "") {
                    $sizeParts[] = $worksheet->getCell($column . $i)->getValue();
                }
            }
            $sizeBaseName = implode('x', $sizeParts);
            $sizeName = "";
            if ($sizeBaseName != "") $sizeName = $sizeBaseName . " cm";

            $productToCreate = [
                "title" =>
                $translater->translate($worksheet->getCell('I' . $i)->getValue())['text'] . " " .
                    $worksheet->getCell('K' . $i)->getValue() . ", " .
                    $translater->translate(
                        explode("\n", $worksheet->getCell('AL' . $i)->getValue())[0]
                    )['text'] . ", "
                    . $sizeName,

                "body_html" => "<strong>" . $translater->translate(
                    $worksheet->getCell('I' . $i)->getValue() . " " . $worksheet->getCell('J' . $i)->getValue()
                )['text'] . "!</strong>",

                "vendor" => $worksheet->getCell('F' . $i)->getValue(),

                "product_type" => $translater->translate(
                    $worksheet->getCell('I' . $i)->getValue()
                )['text'],

                "variants" => [
                    [
                        "sku" => $worksheet->getCell('E' . $i)->getValue(),
                        "price" => $worksheet->getCell('Q' . $i)->getValue(),
                    ]
                ],

                "images" => []
            ];

            foreach ($imageUrlColumns as $column) {
                if ($worksheet->getCell($column . $i)->getValue() != "") {
                    $productToCreate["images"][] = ["src" => $worksheet->getCell($column . $i)->getValue()];
                }
            }

            $this->line($productToCreate["title"] . " creating with " . count($productToCreate["images"]) . " images...");

            $dispatchedJob = CreateProductOnShopifyJob::dispatch($productToCreate);

            $this->info("Create product job has been dipatched.");
            $this->line(" ");
        }

        $this->info("Finished");
    }
}
