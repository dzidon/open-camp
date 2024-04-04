<?php

namespace App\Model\Service\Application;

use App\Model\Entity\Application;
use App\Model\Entity\User;
use App\Model\Repository\CamperRepositoryInterface;
use App\Model\Repository\ContactRepositoryInterface;

/**
 * @inheritDoc
 */
class ApplicationToUserImporter implements ApplicationToUserImporterInterface
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
    public function canImportApplicationToUser(Application $application, User $user): bool
    {
        // billing
        
        if ($application->getNameFull()      !== $user->getNameFull()     ||
            $application->getStreet()        !== $user->getStreet()       ||
            $application->getTown()          !== $user->getTown()         ||
            $application->getZip()           !== $user->getZip()          ||
            $application->getCountry()       !== $user->getCountry()      ||
            $application->getBusinessName()  !== $user->getBusinessName() ||
            $application->getBusinessCin()   !== $user->getBusinessCin()  ||
            $application->getBusinessVatId() !== $user->getBusinessVatId())
        {
            return true;
        }

        // contacts
        
        $applicationContacts = $application->getApplicationContacts();
        $contacts = $this->contactRepository->findByUser($user);
        
        if (!empty($applicationContacts) && empty($contacts))
        {
            return true;
        }
        
        foreach ($applicationContacts as $applicationContact)
        {
            $canImportApplicationContact = true;

            foreach ($contacts as $contact)
            {
                if ($applicationContact->getNameFull()  === $contact->getNameFull()  &&
                    $applicationContact->getEmail()     === $contact->getEmail()     &&
                    $applicationContact->getRole()      === $contact->getRole()      &&
                    $applicationContact->getRoleOther() === $contact->getRoleOther() &&
                    $applicationContact->getPhoneNumber()->equals($contact->getPhoneNumber()))
                {
                    $canImportApplicationContact = false;

                    break;
                }
            }

            if ($canImportApplicationContact)
            {
                return true;
            }
        }

        // campers

        $applicationCampers = $application->getApplicationCampers();
        $campers = $this->camperRepository->findByUser($user);

        if (!empty($applicationCampers) && empty($campers))
        {
            return true;
        }

        foreach ($applicationCampers as $applicationCamper)
        {
            $canImportApplicationCamper = true;

            foreach ($campers as $camper)
            {
                if ($applicationCamper->getNameFull()               === $camper->getNameFull()               &&
                    $applicationCamper->getGender()                 === $camper->getGender()                 &&
                    $applicationCamper->getBornAt()->getTimestamp() === $camper->getBornAt()->getTimestamp() &&
                    $applicationCamper->getNationalIdentifier()     === $camper->getNationalIdentifier()     &&
                    $applicationCamper->getDietaryRestrictions()    === $camper->getDietaryRestrictions()    &&
                    $applicationCamper->getHealthRestrictions()     === $camper->getHealthRestrictions()     &&
                    $applicationCamper->getMedication()             === $camper->getMedication())
                {
                    $canImportApplicationCamper = false;

                    break;
                }
            }

            if ($canImportApplicationCamper)
            {
                return true;
            }
        }
        
        return false;
    }
}