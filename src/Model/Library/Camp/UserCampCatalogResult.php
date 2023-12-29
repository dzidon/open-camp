<?php

namespace App\Model\Library\Camp;

use App\Library\Search\Paginator\PaginatorInterface;
use App\Model\Entity\Camp;
use App\Model\Entity\CampDate;
use App\Model\Entity\CampImage;
use LogicException;

/**
 * @inheritDoc
 */
class UserCampCatalogResult implements UserCampCatalogResultInterface
{
    private PaginatorInterface $paginator;

    private array $campImages = [];

    private array $campDates = [];

    private array $campLowestFullPrices = [];

    public function __construct(PaginatorInterface $paginator, array $campImages, array $campDates)
    {
        // paginator
        foreach ($paginator->getCurrentPageItems() as $camp)
        {
            if (!$camp instanceof Camp)
            {
                throw new LogicException(
                    sprintf('Paginator passed to "%s" can only contain instances of "%s".', $this::class, Camp::class)
                );
            }
        }

        $this->paginator = $paginator;

        // images
        foreach ($campImages as $campImage)
        {
            if (!$campImage instanceof CampImage)
            {
                throw new LogicException(
                    sprintf('Camp images passed to "%s" can only contain instances of "%s".', $this::class, CampImage::class)
                );
            }

            $camp = $campImage->getCamp();
            $campIdString = $camp
                ->getId()
                ->toRfc4122()
            ;

            $this->campImages[$campIdString] = $campImage;
        }

        // dates
        foreach ($campDates as $campDate)
        {
            if (!$campDate instanceof CampDate)
            {
                throw new LogicException(
                    sprintf('Camp dates passed to "%s" can only contain instances of "%s".', $this::class, CampDate::class)
                );
            }

            $camp = $campDate->getCamp();
            $campIdString = $camp
                ->getId()
                ->toRfc4122()
            ;

            $this->campDates[$campIdString][] = $campDate;

            // lowest price
            $price = $campDate->getFullPrice();

            if (array_key_exists($campIdString, $this->campLowestFullPrices) && $price < $this->campLowestFullPrices[$campIdString])
            {
                $this->campLowestFullPrices[$campIdString] = $price;
            }

            if (!array_key_exists($campIdString, $this->campLowestFullPrices))
            {
                $this->campLowestFullPrices[$campIdString] = $price;
            }
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
    public function getCampLowestFullPrice(string|Camp $camp): ?float
    {
        $campIdString = $camp;

        if (!is_string($campIdString))
        {
            $campIdString = $camp
                ->getId()
                ->toRfc4122()
            ;
        }

        if (!array_key_exists($campIdString, $this->campLowestFullPrices))
        {
            return null;
        }

        return $this->campLowestFullPrices[$campIdString];
    }

    /**
     * @inheritDoc
     */
    public function getCampImage(string|Camp $camp): ?CampImage
    {
        $campIdString = $camp;

        if (!is_string($campIdString))
        {
            $campIdString = $camp
                ->getId()
                ->toRfc4122()
            ;
        }

        if (!array_key_exists($campIdString, $this->campImages))
        {
            return null;
        }

        return $this->campImages[$campIdString];
    }

    /**
     * @inheritDoc
     */
    public function getCampDates(string|Camp $camp): array
    {
        $campIdString = $camp;

        if (!is_string($campIdString))
        {
            $campIdString = $camp
                ->getId()
                ->toRfc4122()
            ;
        }

        if (!array_key_exists($campIdString, $this->campDates))
        {
            return [];
        }

        return $this->campDates[$campIdString];
    }
}