<?php

use Illuminate\Support\Facades\Route;
use Shopify\Context;
use Shopify\Auth\FileSessionStorage;
use Shopify\Clients\Rest;

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
    return view('welcome');
});

Route::get('shopifytest', function () {
    //  Show results of shopify api's /products uri
    Context::initialize(
        config('services.shopify.app_key'),
        config('services.shopify.app_password'),
        config('services.shopify.app_scopes'),
        config('services.shopify.app_host'),
        new FileSessionStorage('/tmp/php_sessions'),
        config('services.shopify.api_version'),
        false,
        true
    );
    $client = new Rest(config('services.shopify.store_domain'));
    $response = $client->get('products');
    return $response->getDecodedBody();
});
