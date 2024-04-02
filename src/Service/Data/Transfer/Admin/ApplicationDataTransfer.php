<?php

namespace App\Service\Data\Transfer\Admin;

use App\Library\Data\Admin\ApplicationData;
use App\Library\Data\Common\ApplicationAttachmentData;
use App\Library\Data\Common\ApplicationFormFieldValueData;
use App\Model\Entity\Application;
use App\Model\Event\Admin\ApplicationAttachment\ApplicationAttachmentCreateEvent;
use App\Model\Event\Admin\ApplicationAttachment\ApplicationAttachmentUpdateEvent;
use App\Model\Event\Admin\ApplicationFormFieldValue\ApplicationFormFieldValueCreateEvent;
use App\Model\Event\Admin\ApplicationFormFieldValue\ApplicationFormFieldValueUpdateEvent;
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
        $applicationData->setNote($application->getNote());
        $applicationData->setCustomerChannel($application->getCustomerChannel());
        $applicationData->setCustomerChannelOther($application->getCustomerChannelOther());

        $billingData = $applicationData->getBillingData();
        $this->dataTransfer->fillData($billingData, $application);

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

        $applicationDiscountsData = $applicationData->getApplicationDiscountsData();
        $this->dataTransfer->fillData($applicationDiscountsData, $application);
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

        if ($this->security->isGranted('application_state_update') || $this->security->isGranted('guide_access_state', $application))
        {
            $application->setIsAccepted($applicationData->isAccepted());
        }

        if ($this->security->isGranted('application_update') || $this->security->isGranted('guide_access_update', $application))
        {
            $application->setEmail($applicationData->getEmail());
            $application->setNote($applicationData->getNote());
            $application->setCustomerChannel(
                $applicationData->getCustomerChannel(),
                $applicationData->getCustomerChannelOther()
            );

            $billingData = $applicationData->getBillingData();
            $this->dataTransfer->fillEntity($billingData, $application);

            $applicationFormFieldValuesData = $applicationData->getApplicationFormFieldValuesData();
            $this->fillApplicationFormFieldValues($applicationFormFieldValuesData, $application);

            $applicationAttachmentsData = $applicationData->getApplicationAttachmentsData();
            $this->fillApplicationAttachments($applicationAttachmentsData, $application);
        }

        $applicationDiscountsData = $applicationData->getApplicationDiscountsData();
        $this->dataTransfer->fillEntity($applicationDiscountsData, $application);
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