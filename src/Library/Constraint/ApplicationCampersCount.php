<?php

namespace App\Library\Constraint;

use App\Service\Validator\ApplicationCampersCountValidator;
use Attribute;
use Symfony\Component\Validator\Constraint;

/**
 * Validates that the given number of campers can apply to a camp date.
 */
#[Attribute]
class ApplicationCampersCount extends Constraint
{
    public string $message = 'application_campers_count';
    public string $applicationCamperIdProperty = 'applicationCamperId';
    public string $applicationCampersDataProperty = 'applicationCampersData';
    public string $campDateProperty = 'campDate';

    public function __construct(string $message = null,
                                string $applicationCamperIdProperty = null,
                                string $campDateProperty = null,
                                string $applicationCampersDataProperty = null,
                                array  $groups = null,
                                mixed  $payload = null)
    {
        parent::__construct([], $groups, $payload);

        $this->message = $message ?? $this->message;
        $this->applicationCamperIdProperty = $applicationCamperIdProperty ?? $this->applicationCamperIdProperty;
        $this->campDateProperty = $campDateProperty ?? $this->campDateProperty;
        $this->applicationCampersDataProperty = $applicationCampersDataProperty ?? $this->applicationCampersDataProperty;
    }

    /**
     * @inheritDoc
     */
    public function validatedBy(): string
    {
        return ApplicationCampersCountValidator::class;
    }

    /**
     * @inheritDoc
     */
    public function getTargets(): string
    {
        return self::CLASS_CONSTRAINT;
    }
}