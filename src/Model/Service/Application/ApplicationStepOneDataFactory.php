<?php

namespace App\Model\Service\Application;

use App\Library\Data\Common\ApplicationAttachmentData;
use App\Library\Data\Common\ApplicationCamperData;
use App\Library\Data\Common\ApplicationFormFieldValueData;
use App\Library\Data\Common\ContactData;
use App\Library\Data\User\ApplicationStepOneData;
use App\Model\Entity\CampDate;
use App\Model\Entity\User;
use Symfony\Bundle\SecurityBundle\Security;

/**
 * @inheritDoc
 */
class ApplicationStepOneDataFactory implements ApplicationStepOneDataFactoryInterface
{
    private Security $security;

    private bool $isEuBusinessDataEnabled;

    private bool $isNationalIdentifierEnabled;

    private string $currency;

    private float $tax;

    public function __construct(Security $security,
                                bool     $isEuBusinessDataEnabled,
                                bool     $isNationalIdentifierEnabled,
                                string   $currency,
                                float    $tax)
    {
        $this->security = $security;
        $this->isEuBusinessDataEnabled = $isEuBusinessDataEnabled;
        $this->isNationalIdentifierEnabled = $isNationalIdentifierEnabled;
        $this->currency = $currency;
        $this->tax = $tax;
    }

    /**
     * @inheritDoc
     */
    public function createApplicationStepOneData(CampDate               $campDate,
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

        /** @var User|null $user */
        $user = $this->security->getUser();

        if ($user !== null)
        {
            $applicationData->setEmail($user->getEmail());
            $applicationData->setNameFirst($user->getNameFirst());
            $applicationData->setNameLast($user->getNameLast());
            $applicationData->setStreet($user->getStreet());
            $applicationData->setTown($user->getTown());
            $applicationData->setZip($user->getZip());
            $applicationData->setCountry($user->getCountry());

            $applicationData->setBusinessName($user->getBusinessName());
            $applicationData->setBusinessCin($user->getBusinessCin());
            $applicationData->setBusinessVatId($user->getBusinessVatId());

            if ($applicationData->getBusinessName() !== null || $applicationData->getBusinessCin() !== null || $applicationData->getBusinessVatId() !== null)
            {
                $applicationData->setIsCompany(true);
            }
        }

        return $applicationData;
    }
}