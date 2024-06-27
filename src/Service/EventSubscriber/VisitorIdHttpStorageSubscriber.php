<?php

namespace App\Service\EventSubscriber;

use App\Service\Visitor\VisitorIdHttpStorageInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * Sets the visitor id cookie if it's not set or if the current value is invalid.
 */
class VisitorIdHttpStorageSubscriber
{
    private VisitorIdHttpStorageInterface $visitorIdHttpStorage;

    public function __construct(VisitorIdHttpStorageInterface $visitorIdHttpStorage)
    {
        $this->visitorIdHttpStorage = $visitorIdHttpStorage;
    }

    #[AsEventListener(event: KernelEvents::RESPONSE)]
    public function onKernelController(ResponseEvent $event): void
    {
        $currentVisitorId = $this->visitorIdHttpStorage->getCurrentVisitorId();

        if ($currentVisitorId === null)
        {
            $response = $event->getResponse();
            $newVisitorId = $this->visitorIdHttpStorage->getNewVisitorId();
            $this->visitorIdHttpStorage->setVisitorId($newVisitorId, $response);
        }
    }
}