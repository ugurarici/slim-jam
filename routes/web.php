<?php

use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Shopify\Clients\Rest;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Illuminate\Support\Facades\Storage;
use App\Helpers\TranslateHelper as TranslateClient;
use App\Http\Controllers\TranslationController;
use Inertia\Inertia;
use App\Jobs\CreateShopifyProductsFromExcelJob;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::get('shopifytest', function (Rest $client) {
    //  Show results of shopify api's /products uri
    $response = $client->get('products');
    return $response->getDecodedBody();
});

Route::get('translatetest', function (TranslateClient $translate) {
    return $translate->translate('Hello');
});

Route::get('exceltest', function () {
    //  Get Excel file
    //  Show a cell of each line on the screen
    $reader = IOFactory::createReaderForFile(Storage::path('demo.xls'));
    $reader->setReadDataOnly(true);
    $spreadsheet = $reader->load(Storage::path('demo.xls'));

    $text = "";
    for ($i = 4; $i < 104; $i++) {
        $text .= $spreadsheet->getActiveSheet()->getCell('E' . $i) . "<br>";
    }

    return $text;
});

Route::middleware(['auth:sanctum', 'verified'])->get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->name('dashboard');

Route::resource('translations', TranslationController::class)
    ->only(['index', 'update'])
    ->middleware(['auth:sanctum', 'verified']);

Route::middleware(['auth:sanctum', 'verified'])->get('import-excel', function () {
    return Inertia::render('ImportExcel');
})->name('import-excel');

Route::middleware(['auth:sanctum', 'verified'])->post('import-excel', function () {
    request()->validate([
        'excel' => 'required|file|mimes:xls,xlsx,csv'
    ]);
    $filePath = request()->file('excel')->store('excels');
    CreateShopifyProductsFromExcelJob::dispatch($filePath);
    return redirect()->route('import-excel');
})->name('upload-excel');
