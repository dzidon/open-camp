<?php

namespace App\Model\Library\CampDate;

use App\Library\Search\Paginator\PaginatorInterface;
use App\Model\Entity\CampDate;
use LogicException;

/**
 * @inheritDoc
 */
class AdminApplicationCampDatesResult implements AdminApplicationCampDatesResultInterface
{
    private PaginatorInterface $paginator;

    private array $numbersOfPendingApplications = [];

    public function __construct(PaginatorInterface $paginator, array $numbersOfPendingApplications)
    {
        $campDateIdStrings = [];

        // paginator

        foreach ($paginator->getCurrentPageItems() as $campDate)
        {
            if (!$campDate instanceof CampDate)
            {
                throw new LogicException(
                    sprintf('Paginator passed to "%s" can only contain instances of "%s".', $this::class, CampDate::class)
                );
            }

            $campDateIdString = $campDate
                ->getId()
                ->toRfc4122()
            ;

            $campDateIdStrings[$campDateIdString] = $campDateIdString;
        }

        $this->paginator = $paginator;

        // numbers of pending applications

        foreach ($numbersOfPendingApplications as $campDateIdString => $numberOfPendingApplications)
        {
            if (!array_key_exists($campDateIdString, $campDateIdStrings))
            {
                throw new LogicException(
                    sprintf('Number of pending applications for camp date ID "%s" passed to "%s" is not valid, there is no camp date with the given ID in the paginator result.', $campDateIdString, $this::class)
                );
            }

            if (!is_int($numberOfPendingApplications))
            {
                throw new LogicException(
                    sprintf('Number of pending applications for camp date ID "%s" passed to "%s" is not valid, the value must be an integer.', $campDateIdString, $this::class)
                );
            }

            $this->numbersOfPendingApplications[$campDateIdString] = $numberOfPendingApplications;
        }
    }

    /**
     * @inheritDoc
     */
    public function getPaginator(): PaginatorInterface
    {
        return $this->paginator;
    }

    /**
     * @inheritDoc
     */
    public function getNumberOfPendingApplications(string|CampDate $campDate): ?int
    {
        $campDateIdString = $campDate;

        if (!is_string($campDateIdString))
        {
            $campDateIdString = $campDate
                ->getId()
                ->toRfc4122()
            ;
        }

        if (!array_key_exists($campDateIdString, $this->numbersOfPendingApplications))
        {
            return null;
        }

        return $this->numbersOfPendingApplications[$campDateIdString];
    }
}