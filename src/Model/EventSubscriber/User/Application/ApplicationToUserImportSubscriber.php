<?php

namespace App\Model\EventSubscriber\User\Application;

use App\Library\Data\Common\BillingData;
use App\Library\Data\Common\CamperData;
use App\Library\Data\Common\ContactData;
use App\Model\Event\User\Application\ApplicationToUserImportEvent;
use App\Model\Event\User\Application\ApplicationToUserImportSkipEvent;
use App\Model\Event\User\Camper\CamperCreateEvent;
use App\Model\Event\User\Camper\CamperUpdateEvent;
use App\Model\Event\User\Contact\ContactCreateEvent;
use App\Model\Event\User\Contact\ContactUpdateEvent;
use App\Model\Event\User\User\UserBillingUpdateEvent;
use App\Model\Repository\UserRepositoryInterface;
use App\Service\Data\Registry\DataTransferRegistryInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\RequestStack;

class ApplicationToUserImportSubscriber
{
    private UserRepositoryInterface $userRepository;

    private DataTransferRegistryInterface $dataTransferRegistry;

    private EventDispatcherInterface $eventDispatcher;

    private RequestStack $requestStack;

    private bool $isEuBusinessDataEnabled;

    private bool $isEmailMandatory;

    private bool $isPhoneNumberMandatory;

    private bool $isNationalIdentifierEnabled;

    private string $lastCompletedApplicationIdSessionKey;

    public function __construct(UserRepositoryInterface            $userRepository,
                                DataTransferRegistryInterface      $dataTransferRegistry,
                                EventDispatcherInterface           $eventDispatcher,
                                RequestStack                       $requestStack,
                                bool                               $isEuBusinessDataEnabled,
                                bool                               $isEmailMandatory,
                                bool                               $isPhoneNumberMandatory,
                                bool                               $isNationalIdentifierEnabled,
                                string                             $lastCompletedApplicationIdSessionKey)
    {
        $this->userRepository = $userRepository;
        $this->dataTransferRegistry = $dataTransferRegistry;
        $this->eventDispatcher = $eventDispatcher;
        $this->requestStack = $requestStack;
        $this->isEuBusinessDataEnabled = $isEuBusinessDataEnabled;
        $this->isEmailMandatory = $isEmailMandatory;
        $this->isPhoneNumberMandatory = $isPhoneNumberMandatory;
        $this->isNationalIdentifierEnabled = $isNationalIdentifierEnabled;
        $this->lastCompletedApplicationIdSessionKey = $lastCompletedApplicationIdSessionKey;
    }

    #[AsEventListener(event: ApplicationToUserImportEvent::NAME, priority: 600)]
    public function updateBillingInfo(ApplicationToUserImportEvent $event): void
    {
        $data = $event->getApplicationImportToUserData();
        $allowImportBillingData = $data->allowImportBillingData();
        $skipBillingData = $data->skipBillingData();

        if (!$allowImportBillingData || $skipBillingData)
        {
            return;
        }

        $user = $data->getUser();
        $application = $data->getApplication();
        $billingData = new BillingData(false, $this->isEuBusinessDataEnabled);
        $this->dataTransferRegistry->fillData($billingData, $application);

        $event = new UserBillingUpdateEvent($billingData, $user);
        $event->setIsFlush(false);
        $this->eventDispatcher->dispatch($event, $event::NAME);
    }

    #[AsEventListener(event: ApplicationToUserImportEvent::NAME, priority: 500)]
    public function updateContacts(ApplicationToUserImportEvent $event): void
    {
        $data = $event->getApplicationImportToUserData();
        $applicationImportToUserContactsData = $data->getApplicationImportToUserContactsData();
        $user = $data->getUser();

        foreach ($applicationImportToUserContactsData as $applicationImportToUserContactData)
        {
            $applicationContact = $applicationImportToUserContactData->getApplicationContact();
            $importToContact = $applicationImportToUserContactData->getImportToContact();

            if ($importToContact === null || $importToContact === false)
            {
                continue;
            }

            $contactData = new ContactData($this->isEmailMandatory, $this->isPhoneNumberMandatory);
            $this->dataTransferRegistry->fillData($contactData, $applicationContact);

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
    }

    #[AsEventListener(event: ApplicationToUserImportEvent::NAME, priority: 400)]
    public function updateCampers(ApplicationToUserImportEvent $event): void
    {
        $data = $event->getApplicationImportToUserData();
        $applicationImportToUserCampersData = $data->getApplicationImportToUserCampersData();
        $user = $data->getUser();

        foreach ($applicationImportToUserCampersData as $applicationImportToUserCamperData)
        {
            $applicationCamper = $applicationImportToUserCamperData->getApplicationCamper();
            $importToCamper = $applicationImportToUserCamperData->getImportToCamper();

            if ($importToCamper === null || $importToCamper === false)
            {
                continue;
            }

            $camperData = new CamperData($this->isNationalIdentifierEnabled);
            $this->dataTransferRegistry->fillData($camperData, $applicationCamper);

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

    #[AsEventListener(event: ApplicationToUserImportEvent::NAME, priority: 300)]
    public function setApplicationUser(ApplicationToUserImportEvent $event): void
    {
        $data = $event->getApplicationImportToUserData();
        $application = $data->getApplication();
        $user = $data->getUser();

        if ($application->getUser() === null)
        {
            $application->setUser($user);
        }
    }

    #[AsEventListener(event: ApplicationToUserImportEvent::NAME, priority: 200)]
    public function saveUser(ApplicationToUserImportEvent $event): void
    {
        $data = $event->getApplicationImportToUserData();
        $user = $data->getUser();
        $isFlush = $event->isFlush();

        $this->userRepository->saveUser($user, $isFlush);
    }

    #[AsEventListener(event: ApplicationToUserImportSkipEvent::NAME)]
    #[AsEventListener(event: ApplicationToUserImportEvent::NAME, priority: 100)]
    public function clearSessionVariable(): void
    {
        $currentRequest = $this->requestStack->getCurrentRequest();
        $session = $currentRequest->getSession();

        $session->remove($this->lastCompletedApplicationIdSessionKey);
    }
}