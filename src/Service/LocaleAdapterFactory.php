<?php
namespace LocaleManager\Service;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class LocaleAdapterFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $config        = $serviceLocator->has('Config') ? $serviceLocator->get('Config') : [];
        $config        = isset($config['locale_manager']) ? $config['locale_manager'] : [];
        $adapterConfig = isset($config['adapter']) ? $config['adapter'] : [];

        // Defaults
        $adapterClass = 'LocaleManager\Adapter\PhpArray';

        // Obtain the configured adapter class, if any
        if (isset($adapterConfig['type']) && class_exists($adapterConfig['type'])) {
            $adapterClass = $adapterConfig['type'];
        }

        // Obtain an instance
        $factory = sprintf('%s::factory', $adapterClass);
        return call_user_func($factory, $adapterConfig);
    }
}