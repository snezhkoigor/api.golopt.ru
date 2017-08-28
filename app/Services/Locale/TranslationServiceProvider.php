<?php
/**
 * Created by PhpStorm.
 * User: igorsnezko
 * Date: 14.08.17
 * Time: 23:20
 */

namespace App\Services\Locale;

use Illuminate\Translation\TranslationServiceProvider as ServiceProvider;

class TranslationServiceProvider extends ServiceProvider
{
    protected function registerLoader()
    {
        $this->app->singleton('translation.loader', function ($app) {
            return new TranslationLoader($app['files'], $app['path.lang']);
        });
    }
}