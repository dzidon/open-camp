<?php

namespace App\Service\Data\Transfer\Admin;

use App\Library\Data\Admin\CampDateData;
use App\Model\Entity\CampDate;
use App\Service\Data\Transfer\DataTransferInterface;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;

/**
 * Transfers data from {@link CampDateData} to {@link CampDate} and vice versa.
 */
class CampDateDataTransfer implements DataTransferInterface
{
    private PropertyAccessorInterface $propertyAccessor;

    public function __construct(PropertyAccessorInterface $propertyAccessor)
    {
        $this->propertyAccessor = $propertyAccessor;
    }

    /**
     * @inheritDoc
     */
    public function supports(object $data, object $entity): bool
    {
        return $data instanceof CampDateData && $entity instanceof CampDate;
    }

    /**
     * @inheritDoc
     */
    public function fillData(object $data, object $entity): void
    {
        /** @var CampDateData $campDateData */
        /** @var CampDate $campDate */
        $campDateData = $data;
        $campDate = $entity;

        $campDateData->setStartAt($campDate->getStartAt());
        $campDateData->setEndAt($campDate->getEndAt());
        $campDateData->setPrice($campDate->getPrice());
        $campDateData->setCapacity($campDate->getCapacity());
        $campDateData->setIsOpenAboveCapacity($campDate->isOpenAboveCapacity());
        $campDateData->setIsClosed($campDate->isClosed());
        $campDateData->setDescription($campDate->getDescription());
        $this->propertyAccessor->setValue($campDateData, 'leaders', $campDate->getLeaders());
    }

    /**
     * @inheritDoc
     */
    public function fillEntity(object $data, object $entity): void
    {
        /** @var CampDateData $campDateData */
        /** @var CampDate $campDate */
        $campDateData = $data;
        $campDate = $entity;

        $campDate->setStartAt($campDateData->getStartAt());
        $campDate->setEndAt($campDateData->getEndAt());
        $campDate->setPrice($campDateData->getPrice());
        $campDate->setCapacity($campDateData->getCapacity());
        $campDate->setIsOpenAboveCapacity($campDateData->isOpenAboveCapacity());
        $campDate->setIsClosed($campDateData->isClosed());
        $campDate->setDescription($campDateData->getDescription());
        $this->propertyAccessor->setValue($campDate, 'leaders', $campDateData->getLeaders());
    }
}