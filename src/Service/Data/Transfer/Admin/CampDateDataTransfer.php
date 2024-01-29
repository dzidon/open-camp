<?php

namespace App\Service\Data\Transfer\Admin;

use App\Library\Data\Admin\CampDateAttachmentConfigData;
use App\Library\Data\Admin\CampDateData;
use App\Library\Data\Admin\CampDateFormFieldData;
use App\Library\Data\Admin\CampDatePurchasableItemData;
use App\Library\Data\Admin\CampDateUserData;
use App\Model\Entity\CampDate;
use App\Model\Event\Admin\CampDateAttachmentConfig\CampDateAttachmentConfigCreateEvent;
use App\Model\Event\Admin\CampDateAttachmentConfig\CampDateAttachmentConfigDeleteEvent;
use App\Model\Event\Admin\CampDateAttachmentConfig\CampDateAttachmentConfigUpdateEvent;
use App\Model\Event\Admin\CampDateFormField\CampDateFormFieldCreateEvent;
use App\Model\Event\Admin\CampDateFormField\CampDateFormFieldDeleteEvent;
use App\Model\Event\Admin\CampDateFormField\CampDateFormFieldUpdateEvent;
use App\Model\Event\Admin\CampDatePurchasableItem\CampDatePurchasableItemCreateEvent;
use App\Model\Event\Admin\CampDatePurchasableItem\CampDatePurchasableItemDeleteEvent;
use App\Model\Event\Admin\CampDatePurchasableItem\CampDatePurchasableItemUpdateEvent;
use App\Model\Event\Admin\CampDateUser\CampDateUserCreateEvent;
use App\Model\Event\Admin\CampDateUser\CampDateUserDeleteEvent;
use App\Model\Event\Admin\CampDateUser\CampDateUserUpdateEvent;
use App\Service\Data\Registry\DataTransferRegistryInterface;
use App\Service\Data\Transfer\DataTransferInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Transfers data from {@link CampDateData} to {@link CampDate} and vice versa.
 */
class CampDateDataTransfer implements DataTransferInterface
{
    private DataTransferRegistryInterface $dataTransferRegistry;

    private EventDispatcherInterface $eventDispatcher;

    public function __construct(DataTransferRegistryInterface $dataTransferRegistry, EventDispatcherInterface $eventDispatcher)
    {
        $this->dataTransferRegistry = $dataTransferRegistry;
        $this->eventDispatcher = $eventDispatcher;
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
        $campDateData->setDeposit($campDate->getDeposit());
        $campDateData->setDepositUntil($campDate->getDepositUntil());
        $campDateData->setPriceWithoutDeposit($campDate->getPriceWithoutDeposit());
        $campDateData->setCapacity($campDate->getCapacity());
        $campDateData->setIsOpenAboveCapacity($campDate->isOpenAboveCapacity());
        $campDateData->setIsClosed($campDate->isClosed());
        $campDateData->setIsHidden($campDate->isHidden());
        $campDateData->setDescription($campDate->getDescription());
        $campDateData->setDiscountConfig($campDate->getDiscountConfig());
        $campDateData->setTripLocationPathThere($campDate->getTripLocationPathThere());
        $campDateData->setTripLocationPathBack($campDate->getTripLocationPathBack());

        foreach ($campDate->getCampDateFormFields() as $campDateFormField)
        {
            $campDateFormFieldData = new CampDateFormFieldData();
            $this->dataTransferRegistry->fillData($campDateFormFieldData, $campDateFormField);
            $campDateData->addCampDateFormFieldData($campDateFormFieldData);
        }

        foreach ($campDate->getCampDateAttachmentConfigs() as $campDateAttachmentConfig)
        {
            $campDateAttachmentConfigData = new CampDateAttachmentConfigData();
            $this->dataTransferRegistry->fillData($campDateAttachmentConfigData, $campDateAttachmentConfig);
            $campDateData->addCampDateAttachmentConfigData($campDateAttachmentConfigData);
        }

        foreach ($campDate->getCampDatePurchasableItems() as $campDatePurchasableItem)
        {
            $campDatePurchasableItemData = new CampDatePurchasableItemData();
            $this->dataTransferRegistry->fillData($campDatePurchasableItemData, $campDatePurchasableItem);
            $campDateData->addCampDatePurchasableItemData($campDatePurchasableItemData);
        }

        foreach ($campDate->getCampDateUsers() as $campDateUser)
        {
            $campDateUserData = new CampDateUserData();
            $this->dataTransferRegistry->fillData($campDateUserData, $campDateUser);
            $campDateData->addCampDateUserData($campDateUserData);
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
        $campDate->setDeposit($campDateData->getDeposit());
        $campDate->setDepositUntil($campDateData->getDepositUntil());
        $campDate->setPriceWithoutDeposit($campDateData->getPriceWithoutDeposit());
        $campDate->setCapacity($campDateData->getCapacity());
        $campDate->setIsOpenAboveCapacity($campDateData->isOpenAboveCapacity());
        $campDate->setIsClosed($campDateData->isClosed());
        $campDate->setIsHidden($campDateData->isHidden());
        $campDate->setDescription($campDateData->getDescription());
        $campDate->setDiscountConfig($campDateData->getDiscountConfig());
        $campDate->setTripLocationPathThere($campDateData->getTripLocationPathThere());
        $campDate->setTripLocationPathBack($campDateData->getTripLocationPathBack());

        $campDateFormFieldsData = $campDateData->getCampDateFormFieldsData();
        $this->fillEntityCampDateFormFields($campDateFormFieldsData, $campDate);

        $campDateAttachmentConfigsData = $campDateData->getCampDateAttachmentConfigsData();
        $this->fillEntityCampDateAttachmentConfigs($campDateAttachmentConfigsData, $campDate);

        $campDatePurchasableItemsData = $campDateData->getCampDatePurchasableItemsData();
        $this->fillEntityCampDatePurchasableItems($campDatePurchasableItemsData, $campDate);

        $campDateUsersData = $campDateData->getCampDateUsersData();
        $this->fillEntityCampDateUsers($campDateUsersData, $campDate);
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
                $event = new CampDateFormFieldDeleteEvent($campDateFormField);
                $event->setIsFlush(false);
                $this->eventDispatcher->dispatch($event, $event::NAME);
            }
        }

        // create & update
        foreach ($campDateFormFieldsData as $index => $campDateFormFieldData)
        {
            if (array_key_exists($index, $campDateFormFields))
            {
                $campDateFormField = $campDateFormFields[$index];
                $event = new CampDateFormFieldUpdateEvent($campDateFormFieldData, $campDateFormField);
            }
            else
            {
                $event = new CampDateFormFieldCreateEvent($campDateFormFieldData, $campDate);
            }

            $event->setIsFlush(false);
            $this->eventDispatcher->dispatch($event, $event::NAME);
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
                $event = new CampDateAttachmentConfigDeleteEvent($campDateAttachmentConfig);
                $event->setIsFlush(false);
                $this->eventDispatcher->dispatch($event, $event::NAME);
            }
        }

        // create & update
        foreach ($campDateAttachmentConfigsData as $index => $campDateAttachmentConfigData)
        {
            if (array_key_exists($index, $campDateAttachmentConfigs))
            {
                $campDateAttachmentConfig = $campDateAttachmentConfigs[$index];
                $event = new CampDateAttachmentConfigUpdateEvent($campDateAttachmentConfigData, $campDateAttachmentConfig);
            }
            else
            {
                $event = new CampDateAttachmentConfigCreateEvent($campDateAttachmentConfigData, $campDate);
            }

            $event->setIsFlush(false);
            $this->eventDispatcher->dispatch($event, $event::NAME);
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
                $event = new CampDatePurchasableItemDeleteEvent($campDatePurchasableItem);
                $event->setIsFlush(false);
                $this->eventDispatcher->dispatch($event, $event::NAME);
            }
        }

        // create & update
        foreach ($campDatePurchasableItemsData as $index => $campDatePurchasableItemData)
        {
            if (array_key_exists($index, $campDatePurchasableItems))
            {
                $campDatePurchasableItem = $campDatePurchasableItems[$index];
                $event = new CampDatePurchasableItemUpdateEvent($campDatePurchasableItemData, $campDatePurchasableItem);
            }
            else
            {
                $event = new CampDatePurchasableItemCreateEvent($campDatePurchasableItemData, $campDate);
            }

            $event->setIsFlush(false);
            $this->eventDispatcher->dispatch($event, $event::NAME);
        }
    }

    /**
     * @param CampDateUserData[] $campDateUsersData
     * @param CampDate $campDate
     * @return void
     */
    private function fillEntityCampDateUsers(array $campDateUsersData, CampDate $campDate): void
    {
        $campDateUsers = $campDate->getCampDateUsers();

        // delete
        foreach ($campDateUsers as $index => $campDateUser)
        {
            if (!array_key_exists($index, $campDateUsersData))
            {
                $event = new CampDateUserDeleteEvent($campDateUser);
                $event->setIsFlush(false);
                $this->eventDispatcher->dispatch($event, $event::NAME);
            }
        }

        // create & update
        foreach ($campDateUsersData as $index => $campDateUserData)
        {
            if (array_key_exists($index, $campDateUsers))
            {
                $campDateUser = $campDateUsers[$index];
                $event = new CampDateUserUpdateEvent($campDateUserData, $campDateUser);
            }
            else
            {
                $event = new CampDateUserCreateEvent($campDateUserData, $campDate);
            }

            $event->setIsFlush(false);
            $this->eventDispatcher->dispatch($event, $event::NAME);
        }
    }
}