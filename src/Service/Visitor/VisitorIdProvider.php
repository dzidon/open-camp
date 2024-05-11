<?php

namespace App\Service\Visitor;

use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Uid\UuidV4;

/**
 * @inheritDoc
 */
class VisitorIdProvider implements VisitorIdProviderInterface
{
    private RequestStack $requestStack;

    private UuidV4 $newVisitorId;

    private string $visitorIdCookieName;

    public function __construct(
        RequestStack $requestStack,

        #[Autowire('%app.cookie_name_visitor_id%')]
        string $visitorIdCookieName
    ) {
        $this->requestStack = $requestStack;
        $this->newVisitorId = new UuidV4();
        $this->visitorIdCookieName = $visitorIdCookieName;
    }

    /**
     * @inheritDoc
     */
    public function getCurrentVisitorId(): ?UuidV4
    {
        $request = $this->requestStack->getCurrentRequest();
        $visitorId = $request->cookies->get($this->visitorIdCookieName);

        if ($visitorId === null || !UUid::isValid($visitorId))
        {
            return null;
        }

        return UuidV4::fromString($visitorId);
    }

    /**
     * @inheritDoc
     */
    public function getNewVisitorId(): UuidV4
    {
        return $this->newVisitorId;
    }
}