<?php

namespace App\Service\Visitor;

use Symfony\Component\Uid\UuidV4;

/**
 * Returns visitor ids for the current request.
 */
interface VisitorIdProviderInterface
{
    /**
     * Returns the visitor id of the current request. Null is returned if the id is not set or is invalid.
     *
     * @return null|UuidV4
     */
    public function getCurrentVisitorId(): ?UuidV4;

    /**
     * Returns a new visitor id that can be used for the current request.
     *
     * @return UuidV4
     */
    public function getNewVisitorId(): UuidV4;
}