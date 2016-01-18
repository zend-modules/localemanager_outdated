<?php
namespace LocaleManager\Listener;

use LocaleManager\LocaleManagerInterface;
use Zend\EventManager\AbstractListenerAggregate;
use Zend\EventManager\EventManagerInterface;
use Zend\Mvc\MvcEvent;
use Zend\ServiceManager\ServiceLocatorInterface;

class SendResponseListener extends AbstractListenerAggregate
{
    public function attach(EventManagerInterface $events)
    {
        $this->listeners[] = $events->attach(MvcEvent::EVENT_DISPATCH, array($this, 'onPreDispatch'), 9999);
        $this->listeners[] = $events->attach(MvcEvent::EVENT_DISPATCH_ERROR, array($this, 'onPreDispatch'), 9999);
        $this->listeners[] = $events->attach(MvcEvent::EVENT_FINISH, array($this, 'onFinish'));
        $this->listeners[] = $events->attach(MvcEvent::EVENT_ROUTE, array($this, 'onPreRoute'), 9999);
    }

    protected function getLocale(ServiceLocatorInterface $services)
    {
        if ($services->has('LocaleManager')) {
            $localeManager = $services->get('LocaleManager');
            if ($localeManager instanceof LocaleManagerInterface) {
                return $localeManager->getLocale();
            }
        }

        // Try to get the locale through the translator
        if ($services->has('Translator')) {
            $translator = $services->get('Translator');
            if ($translator instanceof \Zend\Mvc\I18n\Translator) {
                $translator = $translator->getTranslator();
            }

            if (method_exists($translator, 'getLocale')) {
                return \Locale::canonicalize( $translator->getLocale() );
            }
        }

        // If everything went wrong get the default locale
        return \Locale::getDefault();
    }

    /**
     * 
     * Sets the HTTP header for Content-Language.
     * 
     * @param MvcEvent $event
     */
    public function onFinish(MvcEvent $event)
    {
        $response = $event->getResponse();
        if (!$response instanceof \Zend\Http\Response) {
            return;
        }

        $services = $event->getApplication()->getServiceManager();
        $locale   = $this->getLocale($services);

        // For the Content-Language header we need to use the '-' separator.
        $locale = preg_replace('/\_/', '-', $locale);

        // Set the header
        $response->getHeaders()->addHeaderLine('Content-Language', $locale);
    }

    /**
     * Set the default translator before the default dispatch events
     * so the locale has been set and may be used inside the controllers.
     * 
     * @param MvcEvent $event
     */
    public function onPreDispatch(MvcEvent $event) 
    {
        $services = $event->getApplication()->getServiceManager();

        $locale  = $this->getLocale($services);
        
        // ==================================================
        //  HTTP and Console Rendering
        // ==================================================
        // Set the default translator
        if ($services->has('Translator')) {
            $translator = $services->get('Translator');
            if ($translator instanceof \Zend\Mvc\I18n\Translator) {
                $translator = $translator->getTranslator();
            }

            if (method_exists($translator, 'setLocale')) {
                $translator->setLocale($locale);
            }
        }
        
        // ==================================================
        //  HTTP Rendering
        // ==================================================
        $request = $event->getRequest();
        if (!$request instanceof \Zend\Http\Request) {
            return;
        }

        // We need access to the view helper
        if (!$services->has('ViewHelperManager')) {
            return;
        }

        // Get the view helper manager and make sure it is an instance of \Zend\View\HelperPluginManager
        $viewHelperManager = $services->get('ViewHelperManager');
        if (!$viewHelperManager instanceof \Zend\View\HelperPluginManager) {
            return;
        }

        // Get the htmlTag view helper
        if ( $viewHelperManager->has('htmlTag') ) {
            $htmlTag = $viewHelperManager->get('htmlTag');
            $htmlTag->setAttribute('lang', \Locale::getPrimaryLanguage($locale) );
        }
    }

    /**
     * 
     * Sets the router locale if possible.
     * 
     * @param MvcEvent $event
     */
    public function onPreRoute(MvcEvent $event)
    {
        $router = $event->getRouter();
        if ($router instanceof \Zend\I18n\Translator\TranslatorAwareInterface) {
            $translator = $router->getTranslator();
            if ($translator instanceof \Zend\I18n\Translator\TranslatorInterface) {
                if ($translator instanceof \Zend\Mvc\I18n\Translator) {
                    $translator = $translator->getTranslator();
                }

                if (method_exists($translator, 'setLocale')) {
                    $locale = $this->getLocale( $event->getApplication()->getServiceManager() );
                    $translator->setLocale($locale);
                }
            }
        }
    }
}