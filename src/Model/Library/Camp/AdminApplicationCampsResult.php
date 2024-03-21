<?php

namespace App\Model\Library\Camp;

use App\Library\Search\Paginator\PaginatorInterface;
use App\Model\Entity\Camp;
use LogicException;

/**
 * @inheritDoc
 */
class AdminApplicationCampsResult implements AdminApplicationCampsResultInterface
{
    private PaginatorInterface $paginator;

    private array $numbersOfPendingApplications = [];

    public function __construct(PaginatorInterface $paginator, array $numbersOfPendingApplications)
    {
        $campIdStrings = [];

        // paginator

        foreach ($paginator->getCurrentPageItems() as $camp)
        {
            if (!$camp instanceof Camp)
            {
                throw new LogicException(
                    sprintf('Paginator passed to "%s" can only contain instances of "%s".', $this::class, Camp::class)
                );
            }

            $campIdString = $camp
                ->getId()
                ->toRfc4122()
            ;

            $campIdStrings[$campIdString] = $campIdString;
        }

        $this->paginator = $paginator;

        // numbers of pending applications

        foreach ($numbersOfPendingApplications as $campIdString => $numberOfPendingApplications)
        {
            if (!array_key_exists($campIdString, $campIdStrings))
            {
                throw new LogicException(
                    sprintf('Number of pending applications for camp ID "%s" passed to "%s" is not valid, there is no camp with the given ID in the paginator result.', $campIdString, $this::class)
                );
            }

            if (!is_int($numberOfPendingApplications))
            {
                throw new LogicException(
                    sprintf('Number of pending applications for camp ID "%s" passed to "%s" is not valid, the value must be an integer.', $campIdString, $this::class)
                );
            }

            $this->numbersOfPendingApplications[$campIdString] = $numberOfPendingApplications;
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
    public function getNumberOfPendingApplications(string|Camp $camp): ?int
    {
        $campIdString = $camp;

        if (!is_string($campIdString))
        {
            $campIdString = $camp
                ->getId()
                ->toRfc4122()
            ;
        }

        if (!array_key_exists($campIdString, $this->numbersOfPendingApplications))
        {
            return null;
        }

        return $this->numbersOfPendingApplications[$campIdString];
    }
}