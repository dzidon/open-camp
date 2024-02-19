<?php

namespace App\Service\Data\Transfer\User;

use App\Library\Data\User\ApplicationPurchasableItemData;
use App\Library\Data\User\ApplicationPurchasableItemInstanceData;
use App\Model\Entity\ApplicationCamper;
use App\Model\Entity\ApplicationPurchasableItem;
use App\Model\Entity\ApplicationPurchasableItemInstance;
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

        $applicationCamper = $applicationPurchasableItemData->getApplicationCamper();
        $applicationPurchasableItemInstances = $applicationPurchasableItem->getApplicationPurchasableItemInstances();
        $maxAmount = $applicationPurchasableItem->getMaxAmount();
        $application = $applicationPurchasableItem->getApplication();
        $isIndividualMode = $application->isPurchasableItemsIndividualMode();

        if (!$isIndividualMode)
        {
            $maxAmount = $applicationPurchasableItem->getCalculatedMaxAmount();
        }

        $applicationPurchasableItemInstancesFromCamper = [];

        foreach ($applicationPurchasableItemInstances as $applicationPurchasableItemInstance)
        {
            $applicationPurchasableItemInstanceCamper = $applicationPurchasableItemInstance->getApplicationCamper();

            if ($applicationCamper !== $applicationPurchasableItemInstanceCamper)
            {
                continue;
            }

            $applicationPurchasableItemInstancesFromCamper[] = $applicationPurchasableItemInstance;
            $applicationPurchasableItemInstanceData = new ApplicationPurchasableItemInstanceData($maxAmount);
            $this->dataTransfer->fillData($applicationPurchasableItemInstanceData, $applicationPurchasableItemInstance);
            $applicationPurchasableItemData->addApplicationPurchasableItemInstanceData($applicationPurchasableItemInstanceData);
        }

        if (empty($applicationPurchasableItemInstancesFromCamper))
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

        $this->fillEntityApplicationPurchasableItemInstances($applicationPurchasableItemData, $applicationPurchasableItem);
    }

    /**
     * @param ApplicationPurchasableItemData $applicationPurchasableItemData
     * @param ApplicationPurchasableItem $applicationPurchasableItem
     * @return void
     */
    private function fillEntityApplicationPurchasableItemInstances(ApplicationPurchasableItemData $applicationPurchasableItemData,
                                                                   ApplicationPurchasableItem     $applicationPurchasableItem): void
    {
        $applicationPurchasableItemInstancesData = $applicationPurchasableItemData->getApplicationPurchasableItemInstancesData();
        $applicationPurchasableItemInstances = $applicationPurchasableItem->getApplicationPurchasableItemInstances();
        $applicationCamper = $applicationPurchasableItemData->getApplicationCamper();
        $lowestExistingPriority = 0;

        $this->mergeInstancesWithMatchingVariants($applicationPurchasableItemInstancesData);
        $this->filterApplicationPurchasableItemInstancesByCamper($applicationPurchasableItemInstances, $applicationCamper);

        // delete
        foreach ($applicationPurchasableItemInstances as $index => $applicationPurchasableItemInstance)
        {
            if (!array_key_exists($index, $applicationPurchasableItemInstancesData))
            {
                $event = new ApplicationPurchasableItemInstanceDeleteEvent($applicationPurchasableItemInstance);
                $event->setIsFlush(false);
                $this->eventDispatcher->dispatch($event, $event::NAME);

                continue;
            }

            $priority = $applicationPurchasableItemInstance->getPriority();

            if ($priority < $lowestExistingPriority)
            {
                $lowestExistingPriority = $priority;
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
                    $event = new ApplicationPurchasableItemInstanceUpdateEvent(
                        $applicationPurchasableItemInstanceData,
                        $applicationPurchasableItemInstance
                    );
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

                $lowestExistingPriority--;
                $event = new ApplicationPurchasableItemInstanceCreateEvent(
                    $applicationPurchasableItemInstanceData,
                    $applicationPurchasableItem,
                    $lowestExistingPriority
                );

                $event->setApplicationCamper($applicationCamper);
            }

            $event->setIsFlush(false);
            $this->eventDispatcher->dispatch($event, $event::NAME);
        }
    }

    /**
     * @param ApplicationPurchasableItemInstanceData[] $applicationPurchasableItemInstancesData
     * @return void
     */
    private function mergeInstancesWithMatchingVariants(array &$applicationPurchasableItemInstancesData): void
    {
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
    }

    /**
     * @param ApplicationPurchasableItemInstance[] $applicationPurchasableItemInstances
     * @param ApplicationCamper|null $applicationCamper
     * @return void
     */
    private function filterApplicationPurchasableItemInstancesByCamper(array              &$applicationPurchasableItemInstances,
                                                                       ?ApplicationCamper $applicationCamper): void
    {
        $applicationPurchasableItemInstancesForCamper = [];

        foreach ($applicationPurchasableItemInstances as $applicationPurchasableItemInstance)
        {
            $applicationCamperFromInstance = $applicationPurchasableItemInstance->getApplicationCamper();

            if ($applicationCamper === $applicationCamperFromInstance)
            {
                $applicationPurchasableItemInstancesForCamper[] = $applicationPurchasableItemInstance;
            }
        }

        $applicationPurchasableItemInstances = $applicationPurchasableItemInstancesForCamper;
    }
}