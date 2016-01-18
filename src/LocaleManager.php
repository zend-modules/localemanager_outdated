<?php
namespace LocaleManager;

use Locale;
use LocaleManager\Adapter\AdapterInterface;
use LocaleManager\Adapter\PhpArray;
use LocaleManager\Exception;

class LocaleManager implements LocaleManagerInterface
{
    /**
     * 
     * @var AdapterInterface
     */
    protected $adapter = null;

    /**
     * The locale.
     * 
     * @var string
     */
    protected $locale;

    public function __construct(AdapterInterface $adapter = null)
    {
        if ($adapter === null) {
            $adapter = new PhpArray();
        }
        $this->setAdapter($adapter);
    }
    
    /**
     * Get the adapter.
     * 
     * @return AdapterInterface
     */
    public function getAdapter()
    {
        return $this->adapter;
    }

    /**
     * Get the available locale.
     *
     * @return array
     */
    public function getAvailableLocales()
    {
        return $this->getAdapter()->getAvailableLocales();
    }

    /**
     * Get the locale.
     *
     * @return string
     */
    public function getLocale()
    {
        return $this->locale;
    }

    /**
     * Set the adapter.
     *
     * @param AdapterInterface $adapter
     * @return LocaleManager
     */
    public function setAdapter(AdapterInterface $adapter)
    {
        $this->adapter = $adapter;
        return $this;
    }

    /**
     * Set the locale.
     *
     * @param string $locale
     * @return LocaleManager
     */
    public function setLocale($locale)
    {
        $locale = Locale::canonicalize($locale);

        if (strcmp($locale, $this->locale) === 0) {
            // Nothing to do as there is no locale change
            return $this;
        }

        // Try to set the locale for other PHP functions
        $variants = [
            $locale,
            preg_replace('/\_/', '-', $locale)
        ];
        
        if (false === setlocale(LC_ALL, $variants)) {
            throw new Exception\LocaleNotImplementedException(sprintf(
                'The specified locale "%s" does not exist on your system.',
                $locale
            ));
        }

        $this->locale = $locale;
    }
}