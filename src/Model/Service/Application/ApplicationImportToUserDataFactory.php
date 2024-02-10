<?php

namespace App\Model\Service\Application;

use App\Library\Data\User\ApplicationImportToUserCamperData;
use App\Library\Data\User\ApplicationImportToUserContactData;
use App\Library\Data\User\ApplicationImportToUserData;
use App\Model\Entity\Application;
use App\Model\Entity\User;
use App\Model\Repository\CamperRepositoryInterface;
use App\Model\Repository\ContactRepositoryInterface;

/**
 * @inheritDoc
 */
class ApplicationImportToUserDataFactory implements ApplicationImportToUserDataFactoryInterface
{
    private ContactRepositoryInterface $contactRepository;

    private CamperRepositoryInterface $camperRepository;

    public function __construct(ContactRepositoryInterface $contactRepository, CamperRepositoryInterface $camperRepository)
    {
        $this->contactRepository = $contactRepository;
        $this->camperRepository = $camperRepository;
    }

    /**
     * @inheritDoc
     */
    public function createApplicationImportToUserData(Application $application, User $user): ApplicationImportToUserData
    {
        // billing info
        
        $allowImportBillingData = false;

        if ($application->getNameFull()      !== $user->getNameFull()     ||
            $application->getStreet()        !== $user->getStreet()       ||
            $application->getTown()          !== $user->getTown()         ||
            $application->getZip()           !== $user->getZip()          ||
            $application->getCountry()       !== $user->getCountry()      ||
            $application->getBusinessName()  !== $user->getBusinessName() ||
            $application->getBusinessCin()   !== $user->getBusinessCin()  ||
            $application->getBusinessVatId() !== $user->getBusinessVatId())
        {
            $allowImportBillingData = true;
        }

        $applicationImportToUserData = new ApplicationImportToUserData($application, $user, $allowImportBillingData);

        // contacts
        
        $applicationContacts = $application->getApplicationContacts();
        $contacts = $this->contactRepository->findByUser($user);

        foreach ($applicationContacts as $applicationContact)
        {
            $contactChoices = [];

            foreach ($contacts as $contact)
            {
                if ($applicationContact->getNameFull() !== $contact->getNameFull())
                {
                    continue;
                }

                if ($applicationContact->getPhoneNumber()->equals($contact->getPhoneNumber()) &&
                    $applicationContact->getEmail()     === $contact->getEmail()              &&
                    $applicationContact->getRole()      === $contact->getRole()               &&
                    $applicationContact->getRoleOther() === $contact->getRoleOther())
                {
                    continue 2;
                }

                $contactChoices[] = $contact;
            }

            $applicationImportToUserContactData = new ApplicationImportToUserContactData($applicationContact, $contactChoices);
            $applicationImportToUserData->addApplicationImportToUserContactsDatum($applicationImportToUserContactData);
        }
        
        // campers

        $applicationCampers = $application->getApplicationCampers();
        $campers = $this->camperRepository->findByUser($user);

        foreach ($applicationCampers as $applicationCamper)
        {
            $camperChoices = [];

            foreach ($campers as $camper)
            {
                if ($applicationCamper->getNameFull() !== $camper->getNameFull())
                {
                    continue;
                }

                if ($applicationCamper->getGender()                 === $camper->getGender()                 &&
                    $applicationCamper->getBornAt()->getTimestamp() === $camper->getBornAt()->getTimestamp() &&
                    $applicationCamper->getNationalIdentifier()     === $camper->getNationalIdentifier()     &&
                    $applicationCamper->getDietaryRestrictions()    === $camper->getDietaryRestrictions()    &&
                    $applicationCamper->getHealthRestrictions()     === $camper->getHealthRestrictions()     &&
                    $applicationCamper->getMedication()             === $camper->getMedication())
                {
                    continue 2;
                }

                $camperChoices[] = $camper;
            }

            $applicationImportToUserCamperData = new ApplicationImportToUserCamperData($applicationCamper, $camperChoices);
            $applicationImportToUserData->addApplicationImportToUserCampersDatum($applicationImportToUserCamperData);
        }

        return $applicationImportToUserData;
    }
}