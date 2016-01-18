<?php
namespace LocaleManager\Adapter;

use Locale;
use Traversable;
use Zend\Stdlib\ArrayUtils;

class PhpArray implements AdapterInterface
{
    /**
     * The availableLocales array.
     * 
     * @var array
     */
    protected $availableLocales = [];

    /**
     * Add a locale.
     *
     * @param string $locale
     * @return PhpArray
     */
    public function addLocale($locale)
    {
        $locale = Locale::canonicalize($locale);
        if (!in_array($locale, $this->availableLocales)) {
            $this->availableLocales[] = $locale;
        }
        return $this;
    }

    /**
     * Add multiple locales.
     *
     * @param array $locales
     * @return PhpArray
     */
    public function addLocales($locales)
    {
        if (!is_array($locales) && !$locales instanceof Traversable) {
            throw new Exception\InvalidArgumentException('addLocales expects an array or Traversable set of locales');
        }
        
        foreach ($locales as $locale) {
            $this->addLocale($locale);
        }
        
        return $this;
    }
    
    /**
     * Create a new route with given options.
     *
     * @param  array|\Traversable $options
     * @return void
     */
    public static function factory($options = [])
    {
        if ($options instanceof Traversable) {
            $options = ArrayUtils::iteratorToArray($options);
        } elseif (!is_array($options)) {
            throw new Exception\InvalidArgumentException(__METHOD__ . ' expects an array or Traversable set of options');
        }

        $instance = new PhpArray();

        if (isset($options['locales'])) {
            $instance->addLocales($options['locales']);
        }
        
        return $instance;
    }

    /**
     * Get the available locales array.
     *
     * @return array
     */
    public function getAvailableLocales()
    {
        return $this->availableLocales;
    }

    /**
     * Check if locale is available.
     *
     * @param string $locale
     * @return bool
     */
    public function has($locale)
    {
        return in_array($locale, $this->availableLocales);
    }
}