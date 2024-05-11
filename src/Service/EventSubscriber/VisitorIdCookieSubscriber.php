<?php

namespace App\Service\EventSubscriber;

use App\Service\Visitor\VisitorIdProviderInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * Sets the visitor id cookie if it's not set or if the current value is invalid.
 */
class VisitorIdCookieSubscriber
{
    private VisitorIdProviderInterface $visitorIdProvider;

    private string $visitorIdCookieName;

    public function __construct(
        VisitorIdProviderInterface $visitorIdProvider,

        #[Autowire('%app.cookie_name_visitor_id%')]
        string $visitorIdCookieName
    ) {
        $this->visitorIdProvider = $visitorIdProvider;
        $this->visitorIdCookieName = $visitorIdCookieName;
    }

    #[AsEventListener(event: KernelEvents::RESPONSE)]
    public function onKernelController(ResponseEvent $event): void
    {
        $currentVisitorId = $this->visitorIdProvider->getCurrentVisitorId();

        if ($currentVisitorId === null)
        {
            $newVisitorId = $this->visitorIdProvider->getNewVisitorId();
            $newVisitorIdString = $newVisitorId->toRfc4122();
            $cookie = new Cookie($this->visitorIdCookieName, $newVisitorIdString);
            $response = $event->getResponse();
            $response->headers->setCookie($cookie);
        }
    }
}