<?php

namespace App\Library\Data\User;

use App\Model\Entity\Application;
use App\Model\Entity\User;
use Symfony\Component\Validator\Constraints as Assert;

class ApplicationImportToUserData
{
    private Application $application;

    private User $user;

    private bool $allowImportBillingData;

    private bool $skipBillingData = false;

    /** @var ApplicationImportToUserContactData[] */
    #[Assert\Valid]
    private array $applicationImportToUserContactsData = [];

    /** @var ApplicationImportToUserCamperData[] */
    #[Assert\Valid]
    private array $applicationImportToUserCampersData = [];

    public function __construct(Application $application, User $user, bool $allowImportBillingData)
    {
        $this->application = $application;
        $this->user = $user;
        $this->allowImportBillingData = $allowImportBillingData;
    }

    public function getApplication(): Application
    {
        return $this->application;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function allowImportBillingData(): bool
    {
        return $this->allowImportBillingData;
    }

    public function skipBillingData(): bool
    {
        return $this->skipBillingData;
    }

    public function setSkipBillingData(bool $skipBillingData): self
    {
        $this->skipBillingData = $skipBillingData;

        return $this;
    }

    public function getApplicationImportToUserContactsData(): array
    {
        return $this->applicationImportToUserContactsData;
    }

    public function addApplicationImportToUserContactsDatum(ApplicationImportToUserContactData $applicationImportToUserContactData): self
    {
        if (in_array($applicationImportToUserContactData, $this->applicationImportToUserContactsData, true))
        {
            return $this;
        }

        $this->applicationImportToUserContactsData[] = $applicationImportToUserContactData;

        return $this;
    }

    public function removeApplicationImportToUserContactsDatum(ApplicationImportToUserContactData $applicationImportToUserContactData): self
    {
        $key = array_search($applicationImportToUserContactData, $this->applicationImportToUserContactsData, true);

        if ($key === false)
        {
            return $this;
        }

        unset($this->applicationImportToUserContactsData[$key]);

        return $this;
    }

    public function getApplicationImportToUserCampersData(): array
    {
        return $this->applicationImportToUserCampersData;
    }

    public function addApplicationImportToUserCampersDatum(ApplicationImportToUserCamperData $applicationImportToUserCamperData): self
    {
        if (in_array($applicationImportToUserCamperData, $this->applicationImportToUserCampersData, true))
        {
            return $this;
        }

        $this->applicationImportToUserCampersData[] = $applicationImportToUserCamperData;

        return $this;
    }

    public function removeApplicationImportToUserCampersDatum(ApplicationImportToUserCamperData $applicationImportToUserCamperData): self
    {
        $key = array_search($applicationImportToUserCamperData, $this->applicationImportToUserCampersData, true);

        if ($key === false)
        {
            return $this;
        }

        unset($this->applicationImportToUserCampersData[$key]);

        return $this;
    }
}