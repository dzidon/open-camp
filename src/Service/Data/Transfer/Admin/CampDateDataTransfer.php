<?php

namespace App\Service\Data\Transfer\Admin;

use App\Library\Data\Admin\CampDateAttachmentConfigData;
use App\Library\Data\Admin\CampDateData;
use App\Library\Data\Admin\CampDateFormFieldData;
use App\Library\Data\Admin\CampDatePurchasableItemData;
use App\Model\Entity\CampDate;
use App\Model\Entity\CampDateAttachmentConfig;
use App\Model\Entity\CampDateFormField;
use App\Model\Entity\CampDatePurchasableItem;
use App\Model\Repository\CampDateAttachmentConfigRepositoryInterface;
use App\Model\Repository\CampDateFormFieldRepositoryInterface;
use App\Model\Repository\CampDatePurchasableItemRepositoryInterface;
use App\Service\Data\Registry\DataTransferRegistryInterface;
use App\Service\Data\Transfer\DataTransferInterface;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;

/**
 * Transfers data from {@link CampDateData} to {@link CampDate} and vice versa.
 */
class CampDateDataTransfer implements DataTransferInterface
{
    private PropertyAccessorInterface $propertyAccessor;
    private CampDateFormFieldRepositoryInterface $campDateFormFieldRepository;
    private CampDateAttachmentConfigRepositoryInterface $campDateAttachmentConfigRepository;
    private CampDatePurchasableItemRepositoryInterface $campDatePurchasableItemRepository;
    private DataTransferRegistryInterface $dataTransferRegistry;

    public function __construct(PropertyAccessorInterface                   $propertyAccessor,
                                CampDateFormFieldRepositoryInterface        $campDateFormFieldRepository,
                                CampDateAttachmentConfigRepositoryInterface $campDateAttachmentConfigRepository,
                                CampDatePurchasableItemRepositoryInterface  $campDatePurchasableItemRepository,
                                DataTransferRegistryInterface               $dataTransferRegistry)
    {
        $this->propertyAccessor = $propertyAccessor;
        $this->campDateFormFieldRepository = $campDateFormFieldRepository;
        $this->campDateAttachmentConfigRepository = $campDateAttachmentConfigRepository;
        $this->campDatePurchasableItemRepository = $campDatePurchasableItemRepository;
        $this->dataTransferRegistry = $dataTransferRegistry;
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
        $campDateData->setTripLocationPathThere($campDate->getTripLocationPathThere());
        $campDateData->setTripLocationPathBack($campDate->getTripLocationPathBack());
        $this->propertyAccessor->setValue($campDateData, 'leaders', $campDate->getLeaders());

        foreach ($campDate->getCampDateFormFields() as $campDateFormField)
        {
            $campDateFormFieldData = new CampDateFormFieldData();
            $this->dataTransferRegistry->fillData($campDateFormFieldData, $campDateFormField);
            $campDateData->addCampDateFormFieldsDatum($campDateFormFieldData);
        }

        foreach ($campDate->getCampDateAttachmentConfigs() as $campDateAttachmentConfig)
        {
            $campDateAttachmentConfigData = new CampDateAttachmentConfigData();
            $this->dataTransferRegistry->fillData($campDateAttachmentConfigData, $campDateAttachmentConfig);
            $campDateData->addCampDateAttachmentConfigsDatum($campDateAttachmentConfigData);
        }

        foreach ($campDate->getCampDatePurchasableItems() as $campDatePurchasableItem)
        {
            $campDatePurchasableItemData = new CampDatePurchasableItemData();
            $this->dataTransferRegistry->fillData($campDatePurchasableItemData, $campDatePurchasableItem);
            $campDateData->addCampDatePurchasableItemsDatum($campDatePurchasableItemData);
        }
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
        $campDate->setTripLocationPathThere($campDateData->getTripLocationPathThere());
        $campDate->setTripLocationPathBack($campDateData->getTripLocationPathBack());
        $this->propertyAccessor->setValue($campDate, 'leaders', $campDateData->getLeaders());

        $campDateFormFieldsData = $campDateData->getCampDateFormFieldsData();
        $this->fillEntityCampDateFormFields($campDateFormFieldsData, $campDate);

        $campDateAttachmentConfigsData = $campDateData->getCampDateAttachmentConfigsData();
        $this->fillEntityCampDateAttachmentConfigs($campDateAttachmentConfigsData, $campDate);

        $campDatePurchasableItemsData = $campDateData->getCampDatePurchasableItemsData();
        $this->fillEntityCampDatePurchasableItems($campDatePurchasableItemsData, $campDate);
    }

    /**
     * @param CampDateFormFieldData[] $campDateFormFieldsData
     * @param CampDate $campDate
     * @return void
     */
    private function fillEntityCampDateFormFields(array $campDateFormFieldsData, CampDate $campDate): void
    {
        $campDateFormFields = $campDate->getCampDateFormFields();

        // delete
        foreach ($campDateFormFields as $index => $campDateFormField)
        {
            if (!array_key_exists($index, $campDateFormFieldsData))
            {
                $this->campDateFormFieldRepository->removeCampDateFormField($campDateFormField, false);
                $campDate->removeCampDateFormField($campDateFormField);
            }
        }

        // create & update
        foreach ($campDateFormFieldsData as $index => $campDateFormFieldData)
        {
            if (array_key_exists($index, $campDateFormFields))
            {
                $campDateFormField = $campDateFormFields[$index];
            }
            else
            {
                $campDateFormField = new CampDateFormField($campDate, $campDateFormFieldData->getFormField(), $campDateFormFieldData->getPriority());
                $this->campDateFormFieldRepository->saveCampDateFormField($campDateFormField, false);
            }

            $this->dataTransferRegistry->fillEntity($campDateFormFieldData, $campDateFormField);
        }
    }

    /**
     * @param CampDateAttachmentConfigData[] $campDateAttachmentConfigsData
     * @param CampDate $campDate
     * @return void
     */
    private function fillEntityCampDateAttachmentConfigs(array $campDateAttachmentConfigsData, CampDate $campDate): void
    {
        $campDateAttachmentConfigs = $campDate->getCampDateAttachmentConfigs();

        // delete
        foreach ($campDateAttachmentConfigs as $index => $campDateAttachmentConfig)
        {
            if (!array_key_exists($index, $campDateAttachmentConfigsData))
            {
                $this->campDateAttachmentConfigRepository->removeCampDateAttachmentConfig($campDateAttachmentConfig, false);
                $campDate->removeCampDateAttachmentConfig($campDateAttachmentConfig);
            }
        }

        // create & update
        foreach ($campDateAttachmentConfigsData as $index => $campDateAttachmentConfigData)
        {
            if (array_key_exists($index, $campDateAttachmentConfigs))
            {
                $campDateAttachmentConfig = $campDateAttachmentConfigs[$index];
            }
            else
            {
                $campDateAttachmentConfig = new CampDateAttachmentConfig($campDate, $campDateAttachmentConfigData->getAttachmentConfig(), $campDateAttachmentConfigData->getPriority());
                $this->campDateAttachmentConfigRepository->saveCampDateAttachmentConfig($campDateAttachmentConfig, false);
            }

            $this->dataTransferRegistry->fillEntity($campDateAttachmentConfigData, $campDateAttachmentConfig);
        }
    }

    /**
     * @param CampDatePurchasableItemData[] $campDatePurchasableItemsData
     * @param CampDate $campDate
     * @return void
     */
    private function fillEntityCampDatePurchasableItems(array $campDatePurchasableItemsData, CampDate $campDate): void
    {
        $campDatePurchasableItems = $campDate->getCampDatePurchasableItems();

        // delete
        foreach ($campDatePurchasableItems as $index => $campDatePurchasableItem)
        {
            if (!array_key_exists($index, $campDatePurchasableItemsData))
            {
                $this->campDatePurchasableItemRepository->removeCampDatePurchasableItem($campDatePurchasableItem, false);
                $campDate->removeCampDatePurchasableItem($campDatePurchasableItem);
            }
        }

        // create & update
        foreach ($campDatePurchasableItemsData as $index => $campDatePurchasableItemData)
        {
            if (array_key_exists($index, $campDatePurchasableItems))
            {
                $campDatePurchasableItem = $campDatePurchasableItems[$index];
            }
            else
            {
                $campDatePurchasableItem = new CampDatePurchasableItem($campDate, $campDatePurchasableItemData->getPurchasableItem(), $campDatePurchasableItemData->getPriority());
                $this->campDatePurchasableItemRepository->saveCampDatePurchasableItem($campDatePurchasableItem, false);
            }

            $this->dataTransferRegistry->fillEntity($campDatePurchasableItemData, $campDatePurchasableItem);
        }
    }
}