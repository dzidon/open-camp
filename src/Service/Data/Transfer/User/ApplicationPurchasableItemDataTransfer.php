<?php

namespace App\Service\Data\Transfer\User;

use App\Library\Data\User\ApplicationPurchasableItemData;
use App\Library\Data\User\ApplicationPurchasableItemInstanceData;
use App\Model\Entity\ApplicationPurchasableItem;
use App\Model\Event\User\ApplicationPurchasableItemInstance\ApplicationPurchasableItemInstanceCreateEvent;
use App\Model\Event\User\ApplicationPurchasableItemInstance\ApplicationPurchasableItemInstanceDeleteEvent;
use App\Model\Event\User\ApplicationPurchasableItemInstance\ApplicationPurchasableItemInstanceUpdateEvent;
use App\Model\Service\ApplicationPurchasableItemInstance\ApplicationPurchasableItemInstanceDataFactoryInterface;
use App\Service\Data\Registry\DataTransferRegistryInterface;
use App\Service\Data\Transfer\DataTransferInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Transfers data from {@link ApplicationPurchasableItemData} to {@link ApplicationPurchasableItem} and vice versa.
 */
class ApplicationPurchasableItemDataTransfer implements DataTransferInterface
{
    private ApplicationPurchasableItemInstanceDataFactoryInterface $applicationPurchasableItemInstanceDataFactory;

    private DataTransferRegistryInterface $dataTransfer;

    private EventDispatcherInterface $eventDispatcher;

    public function __construct(ApplicationPurchasableItemInstanceDataFactoryInterface $applicationPurchasableItemInstanceDataFactory,
                                DataTransferRegistryInterface                          $dataTransfer,
                                EventDispatcherInterface                               $eventDispatcher)
    {
        $this->applicationPurchasableItemInstanceDataFactory = $applicationPurchasableItemInstanceDataFactory;
        $this->dataTransfer = $dataTransfer;
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * @inheritDoc
     */
    public function supports(object $data, object $entity): bool
    {
        return $data instanceof ApplicationPurchasableItemData && $entity instanceof ApplicationPurchasableItem;
    }

    /**
     * @inheritDoc
     */
    public function fillData(object $data, object $entity): void
    {
        /** @var ApplicationPurchasableItemData $applicationPurchasableItemData */
        /** @var ApplicationPurchasableItem $applicationPurchasableItem */
        $applicationPurchasableItemData = $data;
        $applicationPurchasableItem = $entity;
        $applicationPurchasableItemInstances = $applicationPurchasableItem->getApplicationPurchasableItemInstances();

        foreach ($applicationPurchasableItemInstances as $applicationPurchasableItemInstance)
        {
            $applicationPurchasableItemInstanceData = new ApplicationPurchasableItemInstanceData($applicationPurchasableItem->getCalculatedMaxAmount());
            $this->dataTransfer->fillData($applicationPurchasableItemInstanceData, $applicationPurchasableItemInstance);
            $applicationPurchasableItemData->addApplicationPurchasableItemInstanceData($applicationPurchasableItemInstanceData);
        }

        if (empty($applicationPurchasableItemInstances))
        {
            $applicationPurchasableItemInstanceData = $this->applicationPurchasableItemInstanceDataFactory
                ->createDataFromApplicationPurchasableItem($applicationPurchasableItem)
            ;

            $applicationPurchasableItemInstanceData->setAmount(0);
            $applicationPurchasableItemData->addApplicationPurchasableItemInstanceData($applicationPurchasableItemInstanceData);
        }
    }

    /**
     * @inheritDoc
     */
    public function fillEntity(object $data, object $entity): void
    {
        /** @var ApplicationPurchasableItemData $applicationPurchasableItemData */
        /** @var ApplicationPurchasableItem $applicationPurchasableItem */
        $applicationPurchasableItemData = $data;
        $applicationPurchasableItem = $entity;

        $applicationPurchasableItemInstancesData = $applicationPurchasableItemData->getApplicationPurchasableItemInstancesData();
        $this->fillEntityApplicationPurchasableItemInstances($applicationPurchasableItemInstancesData, $applicationPurchasableItem);
    }

    /**
     * @param ApplicationPurchasableItemInstanceData[] $applicationPurchasableItemInstancesData
     * @param ApplicationPurchasableItem $applicationPurchasableItem
     * @return void
     */
    private function fillEntityApplicationPurchasableItemInstances(array $applicationPurchasableItemInstancesData, ApplicationPurchasableItem $applicationPurchasableItem): void
    {
        $applicationPurchasableItemInstances = $applicationPurchasableItem->getApplicationPurchasableItemInstances();

        // merge instances with matching variants
        $invalidIndices = [];

        foreach ($applicationPurchasableItemInstancesData as $index => $applicationPurchasableItemInstanceData)
        {
            if (in_array($index, $invalidIndices))
            {
                continue;
            }

            foreach ($applicationPurchasableItemInstancesData as $indexOther => $applicationPurchasableItemInstanceDataOther)
            {
                if (in_array($indexOther, $invalidIndices))
                {
                    continue;
                }

                if ($applicationPurchasableItemInstanceDataOther === $applicationPurchasableItemInstanceData)
                {
                    continue;
                }

                $amount = $applicationPurchasableItemInstanceData->getAmount();
                $chosenVariantsOther = $applicationPurchasableItemInstanceDataOther->getChosenApplicationPurchasableItemVariants();
                $chosenVariants = $applicationPurchasableItemInstanceData->getChosenApplicationPurchasableItemVariants();

                if ($chosenVariantsOther === $chosenVariants)
                {
                    $otherAmount = $applicationPurchasableItemInstanceDataOther->getAmount();
                    $newAmount = $amount + $otherAmount;
                    $applicationPurchasableItemInstanceData->setAmount($newAmount);

                    $invalidIndices[] = $indexOther;
                }
            }
        }

        foreach ($invalidIndices as $invalidIndex)
        {
            unset($applicationPurchasableItemInstancesData[$invalidIndex]);
        }

        // delete
        foreach ($applicationPurchasableItemInstances as $index => $applicationPurchasableItemInstance)
        {
            if (!array_key_exists($index, $applicationPurchasableItemInstancesData))
            {
                $event = new ApplicationPurchasableItemInstanceDeleteEvent($applicationPurchasableItemInstance);
                $event->setIsFlush(false);
                $this->eventDispatcher->dispatch($event, $event::NAME);
            }
        }

        // create & update
        foreach ($applicationPurchasableItemInstancesData as $index => $applicationPurchasableItemInstanceData)
        {
            $amount = $applicationPurchasableItemInstanceData->getAmount();

            if (array_key_exists($index, $applicationPurchasableItemInstances))
            {
                $applicationPurchasableItemInstance = $applicationPurchasableItemInstances[$index];

                if ($amount > 0)
                {
                    $event = new ApplicationPurchasableItemInstanceUpdateEvent($applicationPurchasableItemInstanceData, $applicationPurchasableItemInstance);
                }
                else
                {
                    $event = new ApplicationPurchasableItemInstanceDeleteEvent($applicationPurchasableItemInstance);
                }
            }
            else
            {
                if ($amount <= 0)
                {
                    continue;
                }

                $event = new ApplicationPurchasableItemInstanceCreateEvent($applicationPurchasableItemInstanceData, $applicationPurchasableItem);
            }

            $event->setIsFlush(false);
            $this->eventDispatcher->dispatch($event, $event::NAME);
        }
    }
}