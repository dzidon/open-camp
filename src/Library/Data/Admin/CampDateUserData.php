<?php

namespace App\Library\Data\Admin;

use App\Model\Entity\User;
use Symfony\Component\Validator\Constraints as Assert;

class CampDateUserData
{
    #[Assert\NotBlank]
    private ?User $user = null;

    private bool $canUpdateApplicationsState = false;

    private bool $canUpdateApplications = false;

    private bool $canUpdateApplicationPayments = false;

    #[Assert\NotBlank]
    private ?int $priority = 0;

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

    public function canUpdateApplications(): bool
    {
        return $this->canUpdateApplications;
    }

    public function setCanUpdateApplications(bool $canUpdateApplications): self
    {
        $this->canUpdateApplications = $canUpdateApplications;

        return $this;
    }

    public function canUpdateApplicationPayments(): bool
    {
        return $this->canUpdateApplicationPayments;
    }

    public function setCanUpdateApplicationPayments(bool $canUpdateApplicationPayments): self
    {
        $this->canUpdateApplicationPayments = $canUpdateApplicationPayments;

        return $this;
    }

    public function getPriority(): ?int
    {
        return $this->priority;
    }

    public function setPriority(?int $priority): self
    {
        $this->priority = $priority;

        return $this;
    }
}