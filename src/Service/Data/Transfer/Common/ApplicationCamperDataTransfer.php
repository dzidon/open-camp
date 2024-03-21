<?php

namespace App\Service\Data\Transfer\Common;

use App\Library\Data\Common\ApplicationAttachmentData;
use App\Library\Data\Common\ApplicationCamperData;
use App\Library\Data\Common\ApplicationFormFieldValueData;
use App\Model\Entity\ApplicationCamper;
use App\Model\Event\User\ApplicationAttachment\ApplicationAttachmentCreateEvent;
use App\Model\Event\User\ApplicationAttachment\ApplicationAttachmentUpdateEvent;
use App\Model\Event\User\ApplicationFormFieldValue\ApplicationFormFieldValueCreateEvent;
use App\Model\Event\User\ApplicationFormFieldValue\ApplicationFormFieldValueUpdateEvent;
use App\Model\Event\User\ApplicationTripLocationPath\ApplicationTripLocationPathCreateEvent;
use App\Model\Event\User\ApplicationTripLocationPath\ApplicationTripLocationPathUpdateEvent;
use App\Service\Data\Registry\DataTransferRegistryInterface;
use App\Service\Data\Transfer\DataTransferInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Transfers data from {@link ApplicationCamperData} to {@link ApplicationCamper} and vice versa.
 */
class ApplicationCamperDataTransfer implements DataTransferInterface
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
        return $data instanceof ApplicationCamperData && $entity instanceof ApplicationCamper;
    }

    /**
     * @inheritDoc
     */
    public function fillData(object $data, object $entity): void
    {
        /** @var ApplicationCamperData $applicationCamperData */
        /** @var ApplicationCamper $applicationCamper */
        $applicationCamperData = $data;
        $applicationCamper = $entity;

        $camperData = $applicationCamperData->getCamperData();
        $camperData->setNameFirst($applicationCamper->getNameFirst());
        $camperData->setNameLast($applicationCamper->getNameLast());
        $camperData->setBornAt($applicationCamper->getBornAt());
        $camperData->setGender($applicationCamper->getGender());
        $camperData->setDietaryRestrictions($applicationCamper->getDietaryRestrictions());
        $camperData->setHealthRestrictions($applicationCamper->getHealthRestrictions());
        $camperData->setMedication($applicationCamper->getMedication());

        if ($camperData->isNationalIdentifierEnabled())
        {
            if ($applicationCamper->getNationalIdentifier() === null)
            {
                $camperData->setIsNationalIdentifierAbsent(true);
            }
            else
            {
                $camperData->setNationalIdentifier($applicationCamper->getNationalIdentifier());
            }
        }

        foreach ($applicationCamper->getApplicationTripLocationPaths() as $applicationTripLocationPath)
        {
            $this->dataTransferRegistry->fillData($applicationCamperData, $applicationTripLocationPath);
        }

        foreach ($applicationCamper->getApplicationAttachments() as $applicationAttachment)
        {
            $applicationAttachmentData = new ApplicationAttachmentData(
                $applicationAttachment->getMaxSize(),
                $applicationAttachment->getRequiredType(),
                $applicationAttachment->getExtensions(),
                $applicationAttachment->isAlreadyUploaded(),
                $applicationAttachment->getPriority(),
                $applicationAttachment->getLabel(),
                $applicationAttachment->getHelp()
            );

            $applicationCamperData->addApplicationAttachmentsDatum($applicationAttachmentData);
        }

        foreach ($applicationCamper->getApplicationFormFieldValues() as $applicationFormFieldValue)
        {
            $applicationFormFieldValueData = new ApplicationFormFieldValueData(
                $applicationFormFieldValue->getType(),
                $applicationFormFieldValue->isRequired(),
                $applicationFormFieldValue->getOptions(),
                $applicationFormFieldValue->getPriority(),
                $applicationFormFieldValue->getLabel(),
                $applicationFormFieldValue->getHelp()
            );

            $this->dataTransferRegistry->fillData($applicationFormFieldValueData, $applicationFormFieldValue);
            $applicationCamperData->addApplicationFormFieldValuesDatum($applicationFormFieldValueData);
        }
    }

    /**
     * @inheritDoc
     */
    public function fillEntity(object $data, object $entity): void
    {
        /** @var ApplicationCamperData $applicationCamperData */
        /** @var ApplicationCamper $applicationCamper */
        $applicationCamperData = $data;
        $applicationCamper = $entity;

        $camperData = $applicationCamperData->getCamperData();
        $applicationCamper->setNameFirst($camperData->getNameFirst());
        $applicationCamper->setNameLast($camperData->getNameLast());
        $applicationCamper->setBornAt($camperData->getBornAt());
        $applicationCamper->setGender($camperData->getGender());
        $applicationCamper->setDietaryRestrictions($camperData->getDietaryRestrictions());
        $applicationCamper->setHealthRestrictions($camperData->getHealthRestrictions());
        $applicationCamper->setMedication($camperData->getMedication());

        if ($camperData->isNationalIdentifierEnabled())
        {
            if ($camperData->isNationalIdentifierAbsent())
            {
                $applicationCamper->setNationalIdentifier(null);
            }
            else
            {
                $applicationCamper->setNationalIdentifier($camperData->getNationalIdentifier());
            }
        }

        $this->fillApplicationTripLocationPathThere($applicationCamperData, $applicationCamper);
        $this->fillApplicationTripLocationPathBack($applicationCamperData, $applicationCamper);

        $applicationFormFieldValuesData = $applicationCamperData->getApplicationFormFieldValuesData();
        $this->fillApplicationFormFieldValues($applicationFormFieldValuesData, $applicationCamper);

        $applicationAttachmentsData = $applicationCamperData->getApplicationAttachmentsData();
        $this->fillApplicationAttachments($applicationAttachmentsData, $applicationCamper);
    }

    /**
     * @param ApplicationCamperData $applicationCamperData
     * @param ApplicationCamper $applicationCamper
     * @return void
     */
    private function fillApplicationTripLocationPathThere(ApplicationCamperData $applicationCamperData, ApplicationCamper $applicationCamper): void
    {
        if (!$applicationCamperData->hasTripLocationsThere())
        {
            return;
        }

        $tripLocationPathThere = $applicationCamper->getApplicationTripLocationPathThere();

        if ($tripLocationPathThere === null)
        {
            $event = new ApplicationTripLocationPathCreateEvent(true, $applicationCamperData, $applicationCamper);
        }
        else
        {
            $event = new ApplicationTripLocationPathUpdateEvent($applicationCamperData, $tripLocationPathThere);
        }

        $event->setIsFlush(false);
        $this->eventDispatcher->dispatch($event, $event::NAME);
    }

    /**
     * @param ApplicationCamperData $applicationCamperData
     * @param ApplicationCamper $applicationCamper
     * @return void
     */
    private function fillApplicationTripLocationPathBack(ApplicationCamperData $applicationCamperData, ApplicationCamper $applicationCamper): void
    {
        if (!$applicationCamperData->hasTripLocationsBack())
        {
            return;
        }

        $tripLocationPathBack = $applicationCamper->getApplicationTripLocationPathBack();

        if ($tripLocationPathBack === null)
        {
            $event = new ApplicationTripLocationPathCreateEvent(false, $applicationCamperData, $applicationCamper);
        }
        else
        {
            $event = new ApplicationTripLocationPathUpdateEvent($applicationCamperData, $tripLocationPathBack);
        }

        $event->setIsFlush(false);
        $this->eventDispatcher->dispatch($event, $event::NAME);
    }

    /**
     * @param ApplicationFormFieldValueData[] $applicationFormFieldValuesData
     * @param ApplicationCamper $applicationCamper
     * @return void
     */
    private function fillApplicationFormFieldValues(array $applicationFormFieldValuesData, ApplicationCamper $applicationCamper): void
    {
        $applicationFormFieldValues = $applicationCamper->getApplicationFormFieldValues();

        foreach ($applicationFormFieldValuesData as $index => $applicationFormFieldValueData)
        {
            if (array_key_exists($index, $applicationFormFieldValues))
            {
                $applicationFormFieldValue = $applicationFormFieldValues[$index];
                $event = new ApplicationFormFieldValueUpdateEvent($applicationFormFieldValueData, $applicationFormFieldValue);
            }
            else
            {
                $event = new ApplicationFormFieldValueCreateEvent($applicationFormFieldValueData, null, $applicationCamper);
            }

            $event->setIsFlush(false);
            $this->eventDispatcher->dispatch($event, $event::NAME);
        }
    }

    /**
     * @param ApplicationAttachmentData[] $applicationAttachmentsData
     * @param ApplicationCamper $applicationCamper
     * @return void
     */
    private function fillApplicationAttachments(array $applicationAttachmentsData, ApplicationCamper $applicationCamper): void
    {
        $applicationAttachments = $applicationCamper->getApplicationAttachments();

        foreach ($applicationAttachmentsData as $index => $applicationAttachmentData)
        {
            if (array_key_exists($index, $applicationAttachments))
            {
                $applicationAttachment = $applicationAttachments[$index];
                $event = new ApplicationAttachmentUpdateEvent($applicationAttachmentData, $applicationAttachment);
            }
            else
            {
                $event = new ApplicationAttachmentCreateEvent($applicationAttachmentData, null, $applicationCamper);
            }

            $event->setIsFlush(false);
            $this->eventDispatcher->dispatch($event, $event::NAME);
        }
    }
}