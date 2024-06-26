<?php

namespace App\Service\Data\Transfer\User;

use App\Library\Data\Common\ApplicationAttachmentData;
use App\Library\Data\Common\ApplicationFormFieldValueData;
use App\Library\Data\Common\ContactData;
use App\Library\Data\User\ApplicationCamperData;
use App\Library\Data\User\ApplicationStepOneData;
use App\Model\Entity\Application;
use App\Model\Event\User\ApplicationAttachment\ApplicationAttachmentCreateEvent;
use App\Model\Event\User\ApplicationAttachment\ApplicationAttachmentUpdateEvent;
use App\Model\Event\User\ApplicationCamper\ApplicationCamperCreateEvent;
use App\Model\Event\User\ApplicationCamper\ApplicationCamperDeleteEvent;
use App\Model\Event\User\ApplicationCamper\ApplicationCamperUpdateEvent;
use App\Model\Event\User\ApplicationContact\ApplicationContactCreateEvent;
use App\Model\Event\User\ApplicationContact\ApplicationContactDeleteEvent;
use App\Model\Event\User\ApplicationContact\ApplicationContactUpdateEvent;
use App\Model\Event\User\ApplicationFormFieldValue\ApplicationFormFieldValueCreateEvent;
use App\Model\Event\User\ApplicationFormFieldValue\ApplicationFormFieldValueUpdateEvent;
use App\Service\Data\Registry\DataTransferRegistryInterface;
use App\Service\Data\Transfer\DataTransferInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Transfers data from {@link ApplicationStepOneData} to {@link Application} and vice versa.
 */
class ApplicationStepOneDataTransfer implements DataTransferInterface
{
    private DataTransferRegistryInterface $dataTransferRegistry;

    private EventDispatcherInterface $eventDispatcher;

    public function __construct(DataTransferRegistryInterface $dataTransferRegistry,
                                EventDispatcherInterface      $eventDispatcher)
    {
        $this->dataTransferRegistry = $dataTransferRegistry;
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * @inheritDoc
     */
    public function supports(object $data, object $entity): bool
    {
        return $data instanceof ApplicationStepOneData && $entity instanceof Application;
    }

    /**
     * @inheritDoc
     */
    public function fillData(object $data, object $entity): void
    {
        /** @var ApplicationStepOneData $applicationStepOneData */
        /** @var Application $application */
        $applicationStepOneData = $data;
        $application = $entity;

        $applicationStepOneData->setEmail($application->getEmail());
        $billingData = $applicationStepOneData->getBillingData();
        $this->dataTransferRegistry->fillData($billingData, $application);

        foreach ($application->getApplicationAttachments() as $applicationAttachment)
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

            $applicationStepOneData->addApplicationAttachmentsDatum($applicationAttachmentData);
        }

        foreach ($application->getApplicationFormFieldValues() as $applicationFormFieldValue)
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
            $applicationStepOneData->addApplicationFormFieldValuesDatum($applicationFormFieldValueData);
        }

        foreach ($application->getApplicationContacts() as $applicationContact)
        {
            $contactData = new ContactData($application->isEmailMandatory(), $application->isPhoneNumberMandatory());
            $this->dataTransferRegistry->fillData($contactData, $applicationContact);
            $applicationStepOneData->addContactData($contactData);
        }

        foreach ($application->getApplicationCampers() as $applicationCamper)
        {
            $applicationTripLocationPaths = $applicationCamper->getApplicationTripLocationPaths();
            $tripLocationPathThereArray = [];
            $tripLocationPathBackArray = [];

            foreach ($applicationTripLocationPaths as $applicationTripLocationPath)
            {
                if ($applicationTripLocationPath->isThere())
                {
                    $tripLocationPathThereArray = $applicationTripLocationPath->getLocations();
                }
                else
                {
                    $tripLocationPathBackArray = $applicationTripLocationPath->getLocations();
                }
            }

            $applicationCamperData = new ApplicationCamperData(
                $application->isNationalIdentifierEnabled(),
                $application->getCurrency(),
                $tripLocationPathThereArray,
                $tripLocationPathBackArray,
                $applicationCamper->getId(),
            );

            $this->dataTransferRegistry->fillData($applicationCamperData, $applicationCamper);
            $applicationStepOneData->addApplicationCamperData($applicationCamperData);
        }
    }

    /**
     * @inheritDoc
     */
    public function fillEntity(object $data, object $entity): void
    {
        /** @var ApplicationStepOneData $applicationStepOneData */
        /** @var Application $application */
        $applicationStepOneData = $data;
        $application = $entity;

        $application->setEmail($applicationStepOneData->getEmail());
        $billingData = $applicationStepOneData->getBillingData();
        $this->dataTransferRegistry->fillEntity($billingData, $application);

        $applicationFormFieldValuesData = $applicationStepOneData->getApplicationFormFieldValuesData();
        $this->fillApplicationFormFieldValues($applicationFormFieldValuesData, $application);

        $applicationAttachmentsData = $applicationStepOneData->getApplicationAttachmentsData();
        $this->fillApplicationAttachments($applicationAttachmentsData, $application);

        $contactsData = $applicationStepOneData->getContactsData();
        $this->fillEntityApplicationContacts($contactsData, $application);

        $applicationCampersData = $applicationStepOneData->getApplicationCampersData();
        $this->fillEntityApplicationCampers($applicationCampersData, $application);
    }

    /**
     * @param ApplicationFormFieldValueData[] $applicationFormFieldValuesData
     * @param Application $application
     * @return void
     */
    private function fillApplicationFormFieldValues(array $applicationFormFieldValuesData, Application $application): void
    {
        $applicationFormFieldValues = $application->getApplicationFormFieldValues();

        foreach ($applicationFormFieldValuesData as $index => $applicationFormFieldValueData)
        {
            if (array_key_exists($index, $applicationFormFieldValues))
            {
                $applicationFormFieldValue = $applicationFormFieldValues[$index];
                $event = new ApplicationFormFieldValueUpdateEvent($applicationFormFieldValueData, $applicationFormFieldValue);
            }
            else
            {
                $event = new ApplicationFormFieldValueCreateEvent($applicationFormFieldValueData, $application, null);
            }

            $event->setIsFlush(false);
            $this->eventDispatcher->dispatch($event, $event::NAME);
        }
    }

    /**
     * @param ApplicationAttachmentData[] $applicationAttachmentsData
     * @param Application $application
     * @return void
     */
    private function fillApplicationAttachments(array $applicationAttachmentsData, Application $application): void
    {
        $applicationAttachments = $application->getApplicationAttachments();

        foreach ($applicationAttachmentsData as $index => $applicationAttachmentData)
        {
            if (array_key_exists($index, $applicationAttachments))
            {
                $applicationAttachment = $applicationAttachments[$index];
                $event = new ApplicationAttachmentUpdateEvent($applicationAttachmentData, $applicationAttachment);
            }
            else
            {
                $event = new ApplicationAttachmentCreateEvent($applicationAttachmentData, $application, null);
            }

            $event->setIsFlush(false);
            $this->eventDispatcher->dispatch($event, $event::NAME);
        }
    }

    /**
     * @param ContactData[] $contactsData
     * @param Application $application
     * @return void
     */
    private function fillEntityApplicationContacts(array $contactsData, Application $application): void
    {
        $applicationContacts = $application->getApplicationContacts();

        // delete
        foreach ($applicationContacts as $index => $applicationContact)
        {
            if (!array_key_exists($index, $contactsData))
            {
                $event = new ApplicationContactDeleteEvent($applicationContact);
                $event->setIsFlush(false);
                $this->eventDispatcher->dispatch($event, $event::NAME);
            }
        }

        // priority
        $lowestExistingPriority = $application->getLowestApplicationContactPriority();

        // create & update
        foreach ($contactsData as $index => $contactData)
        {
            if (array_key_exists($index, $applicationContacts))
            {
                $applicationContact = $applicationContacts[$index];
                $event = new ApplicationContactUpdateEvent($contactData, $applicationContact);
            }
            else
            {
                $lowestExistingPriority--;
                $event = new ApplicationContactCreateEvent($contactData, $application, $lowestExistingPriority);
            }

            $event->setIsFlush(false);
            $this->eventDispatcher->dispatch($event, $event::NAME);
        }
    }

    /**
     * @param ApplicationCamperData[] $applicationCampersData
     * @param Application $application
     * @return void
     */
    private function fillEntityApplicationCampers(array $applicationCampersData, Application $application): void
    {
        $applicationCampers = $application->getApplicationCampers();

        // delete
        foreach ($applicationCampers as $index => $applicationCamper)
        {
            if (!array_key_exists($index, $applicationCampersData))
            {
                $event = new ApplicationCamperDeleteEvent($applicationCamper);
                $event->setIsFlush(false);
                $this->eventDispatcher->dispatch($event, $event::NAME);
            }
        }

        // priority
        $lowestExistingPriority = $application->getLowestApplicationCamperPriority();

        // create & update
        foreach ($applicationCampersData as $index => $applicationCamperData)
        {
            if (array_key_exists($index, $applicationCampers))
            {
                $applicationCamper = $applicationCampers[$index];
                $event = new ApplicationCamperUpdateEvent($applicationCamperData, $applicationCamper);
            }
            else
            {
                $lowestExistingPriority--;
                $event = new ApplicationCamperCreateEvent($applicationCamperData, $application, $lowestExistingPriority);
            }

            $event->setIsFlush(false);
            $this->eventDispatcher->dispatch($event, $event::NAME);
        }
    }
}