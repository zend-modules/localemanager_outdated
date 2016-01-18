<?php
namespace LocaleManager;

interface LocaleManagerInterface
{
    /**
     * Get the available locale.
     * 
     * @return array
     */
    public function getAvailableLocales();

    /**
     * Get the locale.
     * 
     * @return string
     */
    public function getLocale();

    /**
     * Set the locale.
     *
     * @param string $locale
     * @return LocaleManagerInterface
     */
    public function setLocale($locale);
}