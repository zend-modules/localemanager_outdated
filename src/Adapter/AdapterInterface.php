<?php
namespace LocaleManager\Adapter;

interface AdapterInterface
{
    /**
     * Add a locale.
     * 
     * @param string $locale
     * @return AdapterInterface
     */
    public function addLocale($locale);

    /**
     * Add multiple locales.
     *
     * @param array $locales
     * @return AdapterInterface
     */
    public function addLocales($locales);

   /**
     * Create a new route with given options.
     *
     * @param  array|\Traversable $options
     * @return void
     */
    public static function factory($options);

    /**
     * Get the available locales array.
     * 
     * @return array
     */
    public function getAvailableLocales();

    /**
     * Check if locale is available.
     * 
     * @param string $locale
     * @return bool
     */
    public function has($locale);
}