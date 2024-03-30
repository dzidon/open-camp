<?php

namespace App\Service\Data\Transfer\Common;

use App\Library\Data\Common\ApplicationDiscountsData;
use App\Model\Entity\Application;
use App\Service\Data\Transfer\DataTransferInterface;

/**
 * Transfers data from {@link ApplicationDiscountsData} to {@link Application} and vice versa.
 */
class ApplicationDiscountsDataTransfer implements DataTransferInterface
{
    /**
     * @inheritDoc
     */
    public function supports(object $data, object $entity): bool
    {
        return $data instanceof ApplicationDiscountsData && $entity instanceof Application;
    }

    /**
     * @inheritDoc
     */
    public function fillData(object $data, object $entity): void
    {
        /** @var ApplicationDiscountsData $applicationDiscountsData */
        /** @var Application $application */
        $applicationDiscountsData = $data;
        $application = $entity;

        $discountSiblingsIntervalFrom = $application->getDiscountSiblingsIntervalFrom();
        $discountSiblingsIntervalTo = $application->getDiscountSiblingsIntervalTo();

        if ($discountSiblingsIntervalFrom === null && $discountSiblingsIntervalTo === null)
        {
            $applicationDiscountsData->setDiscountSiblingsInterval(false);
        }
        else
        {
            $applicationDiscountsData->setDiscountSiblingsInterval([
                'from' => $discountSiblingsIntervalFrom,
                'to'   => $discountSiblingsIntervalTo,
            ]);
        }
    }

    /**
     * @inheritDoc
     */
    public function fillEntity(object $data, object $entity): void
    {
        /** @var ApplicationDiscountsData $applicationDiscountsData */
        /** @var Application $application */
        $applicationDiscountsData = $data;
        $application = $entity;

        $discountSiblingsInterval = $applicationDiscountsData->getDiscountSiblingsInterval();

        if ($discountSiblingsInterval === false)
        {
            $application->setDiscountSiblingsInterval(null, null);
        }
        else
        {
            $discountSiblingsIntervalFrom = $discountSiblingsInterval['from'];
            $discountSiblingsIntervalTo = $discountSiblingsInterval['to'];
            $application->setDiscountSiblingsInterval($discountSiblingsIntervalFrom, $discountSiblingsIntervalTo);
        }
    }
}