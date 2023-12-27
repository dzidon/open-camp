<?php

namespace App\Model\Service\Application;

use App\Library\Data\User\ApplicationAttachmentData;
use App\Library\Data\User\ApplicationCamperData;
use App\Library\Data\User\ApplicationStepOneData;
use App\Library\Data\User\ApplicationFormFieldValueData;
use App\Library\Data\User\ContactData;
use App\Model\Entity\CampDate;

/**
 * @inheritDoc
 */
class ApplicationStepOneDataFactory implements ApplicationStepOneDataFactoryInterface
{
    private bool $isEuBusinessDataEnabled;

    private bool $isNationalIdentifierEnabled;

    private string $currency;

    public function __construct(bool   $isEuBusinessDataEnabled,
                                bool   $isNationalIdentifierEnabled,
                                string $currency)
    {
        $this->isEuBusinessDataEnabled = $isEuBusinessDataEnabled;
        $this->isNationalIdentifierEnabled = $isNationalIdentifierEnabled;
        $this->currency = $currency;
    }

    /**
     * @inheritDoc
     */
    public function createApplicationStepOneData(CampDate               $campDate,
                                                 ?ApplicationCamperData $defaultApplicationCamperData = null,
                                                 ?ContactData           $defaultContactData = null): ApplicationStepOneData
    {
        $applicationData = new ApplicationStepOneData($this->isEuBusinessDataEnabled, $this->isNationalIdentifierEnabled, $this->currency);

        if ($defaultContactData !== null)
        {
            $applicationData->addContactData($defaultContactData);
        }

        if ($defaultApplicationCamperData !== null)
        {
            $applicationData->addApplicationCamperData($defaultApplicationCamperData);
        }

        $campDateAttachmentConfigs = $campDate->getCampDateAttachmentConfigs();
        $campDateFormFields = $campDate->getCampDateFormFields();

        foreach ($campDateAttachmentConfigs as $campDateAttachmentConfig)
        {
            $attachmentConfig = $campDateAttachmentConfig->getAttachmentConfig();

            if (!$attachmentConfig->isGlobal())
            {
                continue;
            }

            $extensions = [];

            foreach ($attachmentConfig->getFileExtensions() as $extension)
            {
                $extensions[] = $extension->getExtension();
            }

            $applicationAttachmentData = new ApplicationAttachmentData(
                $attachmentConfig->getMaxSize(),
                $attachmentConfig->getRequiredType(),
                $extensions,
                false,
                $campDateAttachmentConfig->getPriority(),
                $attachmentConfig->getLabel(),
                $attachmentConfig->getHelp()
            );

            $applicationData->addApplicationAttachmentsDatum($applicationAttachmentData);
        }

        foreach ($campDateFormFields as $campDateFormField)
        {
            $formField = $campDateFormField->getFormField();

            if (!$formField->isGlobal())
            {
                continue;
            }

            $formField = $campDateFormField->getFormField();
            $applicationFormFieldValueData = new ApplicationFormFieldValueData(
                $formField->getType(),
                $formField->isRequired(),
                $formField->getOptions(),
                $campDateFormField->getPriority(),
                $formField->getLabel(),
                $formField->getHelp()
            );

            $applicationData->addApplicationFormFieldValuesDatum($applicationFormFieldValueData);
        }

        return $applicationData;
    }
}