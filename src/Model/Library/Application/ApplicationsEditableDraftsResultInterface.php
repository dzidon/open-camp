<?php

namespace App\Model\Library\Application;

use App\Model\Entity\Application;
use Symfony\Component\Uid\UuidV4;

/**
 * Contains information regarding applications and their status as editable drafts.
 */
interface ApplicationsEditableDraftsResultInterface
{
    /**
     * Returns true if the given application is an editable draft.
     * Returns false if it is not.
     * Returns null if there is no information about the given application.
     *
     * @param string|UuidV4|Application $application
     * @return bool|null
     */
    public function isApplicationEditableDraft(string|UuidV4|Application $application): ?bool;
}