<?php
namespace LocaleManager\Service;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use LocaleManager\LocaleManager;
use LocaleManager\Adapter\AdapterInterface;
use Zend\ServiceManager\Exception\ServiceNotCreatedException;

class LocaleManagerFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $serviceLocator)
    { 
        $adapter = null;
        $locale  = null;
        $config  = $serviceLocator->has('Config') ? $serviceLocator->get('Config') : [];
        $config  = isset($config['locale_manager']) ? $config['locale_manager'] : [];

        if ($serviceLocator->has('LocaleManager\Adapter\AdapterInterface')) {
            $adapter = $serviceLocator->get('LocaleManager\Adapter\AdapterInterface');
            if (!$adapter instanceof AdapterInterface) {
                throw new ServiceNotCreatedException(sprintf(
                    'LocaleManager requires that the %s service implement %s; received "%s"',
                    'LocaleManager\Adapter\AdapterInterface',
                    'LocaleManager\Adapter\AdapterInterface',
                    (is_object($adapter) ? get_class($adapter) : gettype($adapter))
                ));
            }
        }

        $manager = new LocaleManager($adapter);

        // Set the current locale
        if (isset($config['locale'])) {
            $locale = $config['locale'];
        } elseif ($serviceLocator->has('Translator')) {
            $translator = $serviceLocator->get('Translator');
            if ($translator instanceof \Zend\Mvc\I18n\Translator) {
                $translator = $translator->getTranslator();
            }

            if (method_exists($translator, 'getLocale')) {
                $locale = $translator->getLocale();
            }
        }

        if ($locale === null) {
            $locale = \Locale::getDefault();
        }

        $manager->setLocale($locale);

        // Return the manager
        return $manager;
    }
}