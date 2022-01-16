<?php

namespace App\Providers;

use Google\Cloud\Translate\V2\TranslateClient;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\ServiceProvider;

class GoogleCloudServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(TranslateClient::class, function ($app) {
            $translationConfig = [
                'keyFilePath' => Storage::path(config('services.google_cloud.key_file')),
                'suppressKeyFileNotice' => true,
            ];

            if (config('services.google_cloud.translation_default_target')) {
                $translationConfig['target'] = config('services.google_cloud.translation_default_target');
            }

            return new TranslateClient($translationConfig);
        });
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
