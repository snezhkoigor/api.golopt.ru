<?php
/**
 * Created by PhpStorm.
 * User: igorsnezko
 * Date: 14.08.17
 * Time: 23:18
 */

namespace App\Services\Locale;

use App\Fragment;
use Cache;
use Illuminate\Translation\FileLoader;

class TranslationLoader extends FileLoader
{
    /**
     * Load the messages for the given locale.
     *
     * @param string $locale
     * @param string $group
     * @param string $namespace
     *
     * @return array
     */
    public function load($locale, $group, $namespace = null)
    {
        if ($namespace !== null && $namespace !== '*') {
            return $this->loadNamespaced($locale, $group, $namespace);
        }

        return Cache::remember("locale.fragments.{$locale}.{$group}", 60,
            function () use ($group, $locale) {
                return Fragment::getGroup($group, $locale);
            });
    }
}