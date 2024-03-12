<?php

namespace App\Library\Data\Admin;

use App\Model\Entity\User;
use Symfony\Component\Validator\Constraints as Assert;

class CampDateUserData
{
    #[Assert\NotBlank]
    private ?User $user = null;

    private bool $canUpdateApplicationsState = false;

    private bool $canManageApplications = false;

    private bool $canManageApplicationPayments = false;

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function canUpdateApplicationsState(): bool
    {
        return $this->canUpdateApplicationsState;
    }

    public function setCanUpdateApplicationsState(bool $canUpdateApplicationsState): self
    {
        $this->canUpdateApplicationsState = $canUpdateApplicationsState;

        return $this;
    }

    public function canManageApplications(): bool
    {
        return $this->canManageApplications;
    }

    public function setCanManageApplications(bool $canManageApplications): self
    {
        $this->canManageApplications = $canManageApplications;

        return $this;
    }

    public function canManageApplicationPayments(): bool
    {
        return $this->canManageApplicationPayments;
    }

    public function setCanManageApplicationPayments(bool $canManageApplicationPayments): self
    {
        $this->canManageApplicationPayments = $canManageApplicationPayments;

        return $this;
    }
}