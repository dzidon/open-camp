<?php

namespace App\Service\Data\Factory\Application;

use App\Library\Data\Common\ApplicationAttachmentData;
use App\Library\Data\Common\ApplicationFormFieldValueData;
use App\Library\Data\Common\ContactData;
use App\Library\Data\User\ApplicationCamperData;
use App\Library\Data\User\ApplicationStepOneData;
use App\Model\Entity\CampDate;
use App\Model\Entity\User;
use App\Service\Data\Registry\DataTransferRegistryInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

/**
 * @inheritDoc
 */
class ApplicationStepOneDataFactory implements ApplicationStepOneDataFactoryInterface
{
    private DataTransferRegistryInterface $dataTransferRegistry;

    private bool $isEuBusinessDataEnabled;

    private bool $isNationalIdentifierEnabled;

    private string $currency;

    private float $tax;

    public function __construct(
        DataTransferRegistryInterface $dataTransferRegistry,

        #[Autowire('%app.eu_business_data%')]
        bool $isEuBusinessDataEnabled,

        #[Autowire('%app.national_identifier%')]
        bool $isNationalIdentifierEnabled,

        #[Autowire('%app.currency%')]
        string $currency,

        #[Autowire('%app.tax%')]
        float $tax
    ) {
        $this->dataTransferRegistry = $dataTransferRegistry;
        $this->isEuBusinessDataEnabled = $isEuBusinessDataEnabled;
        $this->isNationalIdentifierEnabled = $isNationalIdentifierEnabled;
        $this->currency = $currency;
        $this->tax = $tax;
    }

    /**
     * @inheritDoc
     */
    public function createApplicationStepOneData(CampDate               $campDate,
                                                 ?User                  $authenticatedUser = null,
                                                 ?ApplicationCamperData $defaultApplicationCamperData = null,
                                                 ?ContactData           $defaultContactData = null): ApplicationStepOneData
    {
        $applicationData = new ApplicationStepOneData(
            $this->isEuBusinessDataEnabled,
            $this->isNationalIdentifierEnabled,
            $this->currency,
            $this->tax,
            $campDate
        );

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

        if ($authenticatedUser !== null)
        {
            $applicationData->setEmail($authenticatedUser->getEmail());
            $billingData = $applicationData->getBillingData();
            $this->dataTransferRegistry->fillData($billingData, $authenticatedUser);
        }

        return $applicationData;
    }
}