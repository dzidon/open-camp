<?php

namespace App\Service\Data\Transfer\Admin;

use App\Library\Data\Admin\ApplicationData;
use App\Library\Data\Common\ApplicationAttachmentData;
use App\Library\Data\Common\ApplicationFormFieldValueData;
use App\Model\Entity\Application;
use App\Model\Event\User\ApplicationAttachment\ApplicationAttachmentCreateEvent;
use App\Model\Event\User\ApplicationAttachment\ApplicationAttachmentUpdateEvent;
use App\Model\Event\User\ApplicationFormFieldValue\ApplicationFormFieldValueCreateEvent;
use App\Model\Event\User\ApplicationFormFieldValue\ApplicationFormFieldValueUpdateEvent;
use App\Service\Data\Registry\DataTransferRegistryInterface;
use App\Service\Data\Transfer\DataTransferInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Transfers data from {@link ApplicationData} to {@link Application} and vice versa.
 */
class ApplicationDataTransfer implements DataTransferInterface
{
    private DataTransferRegistryInterface $dataTransfer;

    private EventDispatcherInterface $eventDispatcher;

    private Security $security;

    public function __construct(DataTransferRegistryInterface $dataTransfer,
                                EventDispatcherInterface      $eventDispatcher,
                                Security                      $security)
    {
        $this->dataTransfer = $dataTransfer;
        $this->eventDispatcher = $eventDispatcher;
        $this->security = $security;
    }

    /**
     * @inheritDoc
     */
    public function supports(object $data, object $entity): bool
    {
        return $data instanceof ApplicationData && $entity instanceof Application;
    }

    /**
     * @inheritDoc
     */
    public function fillData(object $data, object $entity): void
    {
        /** @var ApplicationData $applicationData */
        /** @var Application $application */
        $applicationData = $data;
        $application = $entity;

        $applicationData->setEmail($application->getEmail());
        $applicationData->setNameFirst($application->getNameFirst());
        $applicationData->setNameLast($application->getNameLast());
        $applicationData->setStreet($application->getStreet());
        $applicationData->setTown($application->getTown());
        $applicationData->setZip($application->getZip());
        $applicationData->setCountry($application->getCountry());
        $applicationData->setIsAccepted($application->isAccepted());

        if ($application->isEuBusinessDataEnabled())
        {
            if ($application->getBusinessName() !== null || $application->getBusinessCin() !== null || $application->getBusinessVatId() !== null)
            {
                $applicationData->setBusinessName($application->getBusinessName());
                $applicationData->setBusinessCin($application->getBusinessCin());
                $applicationData->setBusinessVatId($application->getBusinessVatId());
                $applicationData->setIsCompany(true);
            }
        }

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

            $applicationData->addApplicationAttachmentsDatum($applicationAttachmentData);
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

            $this->dataTransfer->fillData($applicationFormFieldValueData, $applicationFormFieldValue);
            $applicationData->addApplicationFormFieldValuesDatum($applicationFormFieldValueData);
        }
    }

    /**
     * @inheritDoc
     */
    public function fillEntity(object $data, object $entity): void
    {
        /** @var ApplicationData $applicationData */
        /** @var Application $application */
        $applicationData = $data;
        $application = $entity;

        if ($this->security->isGranted('application_state_update') || $this->security->isGranted('application_guide_state', $application))
        {
            $application->setIsAccepted($applicationData->isAccepted());
        }

        if ($this->security->isGranted('application_update') || $this->security->isGranted('application_guide_update', $application))
        {
            $application->setEmail($applicationData->getEmail());
            $application->setNameFirst($applicationData->getNameFirst());
            $application->setNameLast($applicationData->getNameLast());
            $application->setCountry($applicationData->getCountry());
            $application->setStreet($applicationData->getStreet());
            $application->setTown($applicationData->getTown());
            $application->setZip($applicationData->getZip());

            if ($application->isEuBusinessDataEnabled())
            {
                if ($applicationData->isCompany())
                {
                    $application->setBusinessName($applicationData->getBusinessName());
                    $application->setBusinessCin($applicationData->getBusinessCin());
                    $application->setBusinessVatId($applicationData->getBusinessVatId());
                }
                else
                {
                    $application->setBusinessName(null);
                    $application->setBusinessCin(null);
                    $application->setBusinessVatId(null);
                }
            }

            $applicationFormFieldValuesData = $applicationData->getApplicationFormFieldValuesData();
            $this->fillApplicationFormFieldValues($applicationFormFieldValuesData, $application);

            $applicationAttachmentsData = $applicationData->getApplicationAttachmentsData();
            $this->fillApplicationAttachments($applicationAttachmentsData, $application);
        }
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
}