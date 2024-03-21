<?php

namespace App\Model\Service\Application;

use App\Library\Data\Common\BillingData;
use App\Library\Data\Common\CamperData;
use App\Library\Data\Common\ContactData;
use App\Library\Data\User\ApplicationImportToUserData;
use App\Model\Entity\Application;
use App\Model\Entity\User;
use App\Model\Event\User\Camper\CamperCreateEvent;
use App\Model\Event\User\Camper\CamperUpdateEvent;
use App\Model\Event\User\Contact\ContactCreateEvent;
use App\Model\Event\User\Contact\ContactUpdateEvent;
use App\Model\Event\User\User\UserBillingUpdateEvent;
use App\Model\Repository\CamperRepositoryInterface;
use App\Model\Repository\ContactRepositoryInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * @inheritDoc
 */
class ApplicationToUserImporter implements ApplicationToUserImporterInterface
{
    private ContactRepositoryInterface $contactRepository;

    private CamperRepositoryInterface $camperRepository;

    private EventDispatcherInterface $eventDispatcher;

    private bool $isEuBusinessDataEnabled;

    private bool $isEmailMandatory;

    private bool $isPhoneNumberMandatory;

    private bool $isNationalIdentifierEnabled;

    public function __construct(ContactRepositoryInterface $contactRepository,
                                CamperRepositoryInterface  $camperRepository,
                                EventDispatcherInterface   $eventDispatcher,
                                bool                       $isEuBusinessDataEnabled,
                                bool                       $isEmailMandatory,
                                bool                       $isPhoneNumberMandatory,
                                bool                       $isNationalIdentifierEnabled)
    {
        $this->contactRepository = $contactRepository;
        $this->camperRepository = $camperRepository;
        $this->eventDispatcher = $eventDispatcher;
        $this->isEuBusinessDataEnabled = $isEuBusinessDataEnabled;
        $this->isEmailMandatory = $isEmailMandatory;
        $this->isPhoneNumberMandatory = $isPhoneNumberMandatory;
        $this->isNationalIdentifierEnabled = $isNationalIdentifierEnabled;
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

    /**
     * @inheritDoc
     */
    public function importApplicationDataToUser(ApplicationImportToUserData $data): void
    {
        $application = $data->getApplication();
        $user = $data->getUser();
        $skipBillingData = $data->skipBillingData();
        $allowImportBillingData = $data->allowImportBillingData();
        $applicationImportToUserContactsData = $data->getApplicationImportToUserContactsData();
        $applicationImportToUserCampersData = $data->getApplicationImportToUserCampersData();

        // billing info

        if ($allowImportBillingData && !$skipBillingData)
        {
            $billingData = new BillingData($this->isEuBusinessDataEnabled);

            $billingData->setNameFirst($application->getNameFirst());
            $billingData->setNameLast($application->getNameLast());
            $billingData->setCountry($application->getCountry());
            $billingData->setStreet($application->getStreet());
            $billingData->setTown($application->getTown());
            $billingData->setZip($application->getZip());

            if ($this->isEuBusinessDataEnabled)
            {
                if ($application->getBusinessName()  !== null ||
                    $application->getBusinessCin()   !== null ||
                    $application->getBusinessVatId() !== null)
                {
                    $billingData->setBusinessName($application->getBusinessName());
                    $billingData->setBusinessCin($application->getBusinessCin());
                    $billingData->setBusinessVatId($application->getBusinessVatId());
                    $billingData->setIsCompany(true);
                }
            }

            $event = new UserBillingUpdateEvent($billingData, $user);
            $event->setIsFlush(false);
            $this->eventDispatcher->dispatch($event, $event::NAME);
        }

        // contacts

        foreach ($applicationImportToUserContactsData as $applicationImportToUserContactData)
        {
            $applicationContact = $applicationImportToUserContactData->getApplicationContact();
            $importToContact = $applicationImportToUserContactData->getImportToContact();

            if ($importToContact === null || $importToContact === false)
            {
                continue;
            }

            $contactData = new ContactData($this->isEmailMandatory, $this->isPhoneNumberMandatory);
            $contactData->setNameFirst($applicationContact->getNameFirst());
            $contactData->setNameLast($applicationContact->getNameLast());
            $contactData->setEmail($applicationContact->getEmail());
            $contactData->setPhoneNumber($applicationContact->getPhoneNumber());
            $contactData->setRole($applicationContact->getRole());
            $contactData->setRoleOther($applicationContact->getRoleOther());

            if ($importToContact === true)
            {
                $event = new ContactCreateEvent($contactData, $user);
            }
            else
            {
                $event = new ContactUpdateEvent($contactData, $importToContact);
            }

            $event->setIsFlush(false);
            $this->eventDispatcher->dispatch($event, $event::NAME);
        }

        // campers

        foreach ($applicationImportToUserCampersData as $applicationImportToUserCamperData)
        {
            $applicationCamper = $applicationImportToUserCamperData->getApplicationCamper();
            $importToCamper = $applicationImportToUserCamperData->getImportToCamper();

            if ($importToCamper === null || $importToCamper === false)
            {
                continue;
            }

            $camperData = new CamperData($this->isNationalIdentifierEnabled);
            $camperData->setNameFirst($applicationCamper->getNameFirst());
            $camperData->setNameLast($applicationCamper->getNameLast());
            $camperData->setGender($applicationCamper->getGender());
            $camperData->setBornAt($applicationCamper->getBornAt());
            $camperData->setDietaryRestrictions($applicationCamper->getDietaryRestrictions());
            $camperData->setHealthRestrictions($applicationCamper->getHealthRestrictions());
            $camperData->setMedication($applicationCamper->getMedication());

            if ($this->isNationalIdentifierEnabled)
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

            if ($importToCamper === true)
            {
                $event = new CamperCreateEvent($camperData, $user);
            }
            else
            {
                $event = new CamperUpdateEvent($camperData, $importToCamper);
            }

            $event->setIsFlush(false);
            $this->eventDispatcher->dispatch($event, $event::NAME);
        }
    }
}