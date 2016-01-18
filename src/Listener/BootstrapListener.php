<?php
namespace LocaleManager\Listener;

use Zend\EventManager\AbstractListenerAggregate;
use Zend\EventManager\EventManagerInterface;
use Zend\Mvc\MvcEvent;

class BootstrapListener extends AbstractListenerAggregate
{
    public function attach(EventManagerInterface $events)
    {
        $this->listeners[] = $events->attach(MvcEvent::EVENT_BOOTSTRAP, array($this, 'onBootstrap'));
    }

    public function onBootstrap(MvcEvent $e)
    {
        $eventManager         = $e->getApplication()->getEventManager();
        $serviceManager       = $e->getApplication()->getServiceManager();

        $sendResponseListener = new SendResponseListener();
        $sendResponseListener->attach($eventManager);
    }
}