<?php

namespace App\Model\Library\Camp;

use App\Model\Entity\CampDate;
use LogicException;

/**
 * @inheritDoc
 */
class UserUpcomingCampDatesResult implements UserUpcomingCampDatesResultInterface
{
    private array $campDates = [];

    private array $openCampDates = [];

    /**
     * @param CampDate[] $campDates
     * @param CampDate[] $openCampDates
     */
    public function __construct(array $campDates, array $openCampDates)
    {
        // dates

        foreach ($campDates as $campDate)
        {
            if (!$campDate instanceof CampDate)
            {
                throw new LogicException(
                    sprintf('Camp dates passed to "%s" can only contain instances of "%s".', $this::class, CampDate::class)
                );
            }

            $campDateIdString = $campDate
                ->getId()
                ->toRfc4122()
            ;

            $this->campDates[$campDateIdString] = $campDate;
        }

        // open dates

        foreach ($openCampDates as $openCampDate)
        {
            if (!in_array($openCampDate, $this->campDates))
            {
                throw new LogicException(
                    sprintf('Open camp dates passed to "%s" must be present in $campDates.', $this::class)
                );
            }

            $openCampDateIdString = $openCampDate
                ->getId()
                ->toRfc4122()
            ;

            $this->openCampDates[$openCampDateIdString] = $openCampDate;
        }
    }

    /**
     * @inheritDoc
     */
    public function getCampDates(): array
    {
        return $this->campDates;
    }

    /**
     * @inheritDoc
     */
    public function isCampDateOpen(string|CampDate $campDate): bool
    {
        $campDateIdString = $campDate;

        if (!is_string($campDateIdString))
        {
            $campDateIdString = $campDate
                ->getId()
                ->toRfc4122()
            ;
        }

        return array_key_exists($campDateIdString, $this->openCampDates);
    }
}