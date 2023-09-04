<?php

namespace App\Library\Data\Admin;

use libphonenumber\PhoneNumber;
use Misd\PhoneNumberBundle\Validator\Constraints\PhoneNumber as AssertPhoneNumber;

class ProfileData
{
    #[AssertPhoneNumber]
    private ?PhoneNumber $leaderPhoneNumber = null;

    public function getLeaderPhoneNumber(): ?PhoneNumber
    {
        if ($this->leaderPhoneNumber !== null)
        {
            return clone $this->leaderPhoneNumber;
        }

        return $this->leaderPhoneNumber;
    }

    public function setLeaderPhoneNumber(?PhoneNumber $leaderPhoneNumber): self
    {
        if ($leaderPhoneNumber !== null)
        {
            $leaderPhoneNumber = clone $leaderPhoneNumber;
        }

        $this->leaderPhoneNumber = $leaderPhoneNumber;

        return $this;
    }
}