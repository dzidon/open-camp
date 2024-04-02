<?php

namespace App\Service\Data\Factory\ApplicationCamper;

use App\Library\Data\User\ApplicationCamperData as UserApplicationCamperData;
use App\Library\Data\Admin\ApplicationCamperData as AdminApplicationCamperData;
use App\Library\Data\Common\ApplicationAttachmentData;
use App\Library\Data\Common\ApplicationFormFieldValueData;
use App\Model\Entity\Application;
use App\Model\Entity\ApplicationCamper;
use App\Model\Entity\CampDate;
use App\Model\Repository\TripLocationRepositoryInterface;

/**
 * @inheritDoc
 */
class ApplicationCamperDataFactory implements ApplicationCamperDataFactoryInterface
{
    private TripLocationRepositoryInterface $tripLocationRepository;

    private bool $isNationalIdentifierEnabled;

    private string $currency;

    public function __construct(TripLocationRepositoryInterface $tripLocationRepository,
                                bool                            $isNationalIdentifierEnabled,
                                string                          $currency)
    {
        $this->tripLocationRepository = $tripLocationRepository;
        $this->isNationalIdentifierEnabled = $isNationalIdentifierEnabled;
        $this->currency = $currency;
    }

    /**
     * @inheritDoc
     */
    public function createFromCampDateForUserModule(CampDate $campDate): UserApplicationCamperData
    {
        $tripLocationPathThere = $campDate->getTripLocationPathThere();
        $tripLocationPathBack = $campDate->getTripLocationPathBack();
        $tripLocationPathThereArray = [];
        $tripLocationPathBackArray = [];

        // trip there

        if ($tripLocationPathThere !== null)
        {
            $tripLocationsThere = $this->tripLocationRepository->findByTripLocationPath($tripLocationPathThere);

            foreach ($tripLocationsThere as $tripLocationThere)
            {
                $tripLocationPathThereArray[] = [
                    'name'  => $tripLocationThere->getName(),
                    'price' => $tripLocationThere->getPrice(),
                ];
            }
        }

        // trip back

        if ($tripLocationPathBack !== null)
        {
            $tripLocationsBack = $this->tripLocationRepository->findByTripLocationPath($tripLocationPathBack);

            foreach ($tripLocationsBack as $tripLocationBack)
            {
                $tripLocationPathBackArray[] = [
                    'name'  => $tripLocationBack->getName(),
                    'price' => $tripLocationBack->getPrice(),
                ];
            }
        }

        // camper

        $applicationCamperData = new UserApplicationCamperData(
            $this->isNationalIdentifierEnabled,
            $this->currency,
            $tripLocationPathThereArray,
            $tripLocationPathBackArray,
        );

        $campDateAttachmentConfigs = $campDate->getCampDateAttachmentConfigs();
        $campDateFormFields = $campDate->getCampDateFormFields();

        // attachment configs

        foreach ($campDateAttachmentConfigs as $campDateAttachmentConfig)
        {
            $attachmentConfig = $campDateAttachmentConfig->getAttachmentConfig();

            if ($attachmentConfig->isGlobal())
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
                $attachmentConfig->getRequiredType(), $extensions,
                false,
                $campDateAttachmentConfig->getPriority(),
                $attachmentConfig->getLabel(),
                $attachmentConfig->getHelp()
            );

            $applicationCamperData->addApplicationAttachmentsDatum($applicationAttachmentData);
        }

        // form fields

        foreach ($campDateFormFields as $campDateFormField)
        {
            $formField = $campDateFormField->getFormField();

            if ($formField->isGlobal())
            {
                continue;
            }

            $applicationFormFieldValueData = new ApplicationFormFieldValueData(
                $formField->getType(),
                $formField->isRequired(),
                $formField->getOptions(),
                $campDateFormField->getPriority(),
                $formField->getLabel(),
                $formField->getHelp()
            );

            $applicationCamperData->addApplicationFormFieldValuesDatum($applicationFormFieldValueData);
        }

        return $applicationCamperData;
    }

    /**
     * @inheritDoc
     */
    public function getCallableFromCampDateForUserModule(CampDate $campDate): callable
    {
        $factory = $this;

        return function () use ($factory, $campDate): UserApplicationCamperData
        {
            return $factory->createFromCampDateForUserModule($campDate);
        };
    }

    /**
     * @inheritDoc
     */
    public function createFromApplicationForUserModule(Application $application): UserApplicationCamperData
    {
        return $this->createFromApplication($application, true);
    }

    /**
     * @inheritDoc
     */
    public function getCallableFromApplicationForUserModule(Application $application): callable
    {
        $factory = $this;

        return function () use ($factory, $application): UserApplicationCamperData
        {
            return $factory->createFromApplicationForUserModule($application);
        };
    }

    /**
     * @inheritDoc
     */
    public function createFromApplicationCamperForUserModule(ApplicationCamper $applicationCamper): UserApplicationCamperData
    {
        return $this->createFromApplicationCamper($applicationCamper, true);
    }

    /**
     * @inheritDoc
     */
    public function getCallableFromApplicationCamperForUserModule(ApplicationCamper $applicationCamper): callable
    {
        $factory = $this;

        return function () use ($factory, $applicationCamper): UserApplicationCamperData
        {
            return $factory->createFromApplicationCamperForUserModule($applicationCamper);
        };
    }

    /**
     * @inheritDoc
     */
    public function createFromApplicationForAdminModule(Application $application): AdminApplicationCamperData
    {
        return $this->createFromApplication($application, false);
    }

    /**
     * @inheritDoc
     */
    public function getCallableFromApplicationForAdminModule(Application $application): callable
    {
        $factory = $this;

        return function () use ($factory, $application): AdminApplicationCamperData
        {
            return $factory->createFromApplicationForAdminModule($application);
        };
    }

    /**
     * @inheritDoc
     */
    public function createFromApplicationCamperForAdminModule(ApplicationCamper $applicationCamper): AdminApplicationCamperData
    {
        return $this->createFromApplicationCamper($applicationCamper, false);
    }

    /**
     * @inheritDoc
     */
    public function getCallableFromApplicationCamperForAdminModule(ApplicationCamper $applicationCamper): callable
    {
        $factory = $this;

        return function () use ($factory, $applicationCamper): AdminApplicationCamperData
        {
            return $factory->createFromApplicationCamperForAdminModule($applicationCamper);
        };
    }

    private function createFromApplication(Application $application, bool $forUserModule): UserApplicationCamperData|AdminApplicationCamperData
    {
        $dataClass = AdminApplicationCamperData::class;

        if ($forUserModule)
        {
            $dataClass = UserApplicationCamperData::class;
        }

        $applicationCampers = $application->getApplicationCampers();
        $isNationalIdentifierEnabled = $application->isNationalIdentifierEnabled();
        $currency = $application->getCurrency();

        if (empty($applicationCampers))
        {
            return new $dataClass($isNationalIdentifierEnabled, $currency, [], []);
        }

        $firstKey = array_key_first($applicationCampers);
        $referenceApplicationCamper = $applicationCampers[$firstKey];

        $tripLocationThere = [];
        $tripLocationBack = [];
        $applicationTripLocationPathThere = $referenceApplicationCamper->getApplicationTripLocationPathThere();
        $applicationTripLocationPathBack = $referenceApplicationCamper->getApplicationTripLocationPathBack();

        if ($applicationTripLocationPathThere !== null)
        {
            $tripLocationThere = $applicationTripLocationPathThere->getLocations();
        }

        if ($applicationTripLocationPathBack !== null)
        {
            $tripLocationBack = $applicationTripLocationPathBack->getLocations();
        }

        $newData = new $dataClass(
            $isNationalIdentifierEnabled,
            $currency,
            $tripLocationThere,
            $tripLocationBack,
        );

        foreach ($referenceApplicationCamper->getApplicationAttachments() as $applicationAttachment)
        {
            $newApplicationAttachmentData = new ApplicationAttachmentData(
                $applicationAttachment->getMaxSize(),
                $applicationAttachment->getRequiredType(),
                $applicationAttachment->getExtensions(),
                false,
                $applicationAttachment->getPriority(),
                $applicationAttachment->getLabel(),
                $applicationAttachment->getHelp()
            );

            $newData->addApplicationAttachmentsDatum($newApplicationAttachmentData);
        }

        foreach ($referenceApplicationCamper->getApplicationFormFieldValues() as $applicationFormFieldValue)
        {
            $newApplicationFormFieldValueData = new ApplicationFormFieldValueData(
                $applicationFormFieldValue->getType(),
                $applicationFormFieldValue->isRequired(),
                $applicationFormFieldValue->getOptions(),
                $applicationFormFieldValue->getPriority(),
                $applicationFormFieldValue->getLabel(),
                $applicationFormFieldValue->getHelp()
            );

            $newData->addApplicationFormFieldValuesDatum($newApplicationFormFieldValueData);
        }

        return $newData;
    }

    private function createFromApplicationCamper(ApplicationCamper $applicationCamper, bool $forUserModule): UserApplicationCamperData|AdminApplicationCamperData
    {
        $dataClass = AdminApplicationCamperData::class;

        if ($forUserModule)
        {
            $dataClass = UserApplicationCamperData::class;
        }

        $applicationTripLocationPaths = $applicationCamper->getApplicationTripLocationPaths();
        $application = $applicationCamper->getApplication();
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

        return new $dataClass(
            $application->isNationalIdentifierEnabled(),
            $application->getCurrency(),
            $tripLocationPathThereArray,
            $tripLocationPathBackArray,
            $applicationCamper->getId(),
        );
    }
}