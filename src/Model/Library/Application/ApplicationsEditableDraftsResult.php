<?php

namespace App\Model\Library\Application;

use App\Model\Entity\Application;
use LogicException;
use Symfony\Component\Uid\UuidV4;

/**
 * @inheritDoc
 */
class ApplicationsEditableDraftsResult implements ApplicationsEditableDraftsResultInterface
{
    private array $isApplicationEditableDraft = [];

    public function __construct(array $isApplicationEditableDraft)
    {
        $exception = new LogicException(
            sprintf('Array $isApplicationEditableDraft passed to %s::__construct must have the following shape: [["idString" (string) => "isEditableDraft" (bool)], ...]', ApplicationsEditableDraftsResult::class)
        );

        foreach ($isApplicationEditableDraft as $applicationIdString => $isEditableDraft)
        {
            if (!is_string($applicationIdString))
            {
                throw $exception;
            }

            if (!is_bool($isEditableDraft))
            {
                throw $exception;
            }

            $this->isApplicationEditableDraft[$applicationIdString] = $isEditableDraft;
        }
    }

    /**
     * @inheritDoc
     */
    public function isApplicationEditableDraft(string|UuidV4|Application $application): ?bool
    {
        if ($application instanceof Application)
        {
            $application = $application->getId();
        }

        if ($application instanceof UuidV4)
        {
            $application = $application->toRfc4122();
        }

        $applicationIdString = $application;

        if (!array_key_exists($applicationIdString, $this->isApplicationEditableDraft))
        {
            return null;
        }

        return $this->isApplicationEditableDraft[$applicationIdString];
    }
}