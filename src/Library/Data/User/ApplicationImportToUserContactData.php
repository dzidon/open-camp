<?php

namespace App\Library\Data\User;

use App\Model\Entity\ApplicationContact;
use App\Model\Entity\Contact;
use Symfony\Component\Validator\Constraints as Assert;

class ApplicationImportToUserContactData
{
    private ApplicationContact $applicationContact;

    /** @var Contact[]|bool[] */
    private array $importToContactChoices = [false, true];

    #[Assert\Choice(callback: 'getImportToContactChoices')]
    #[Assert\NotNull]
    private null|bool|Contact $importToContact = true;

    /**
     * @param ApplicationContact $applicationContact
     * @param Contact[] $contactChoices
     */
    public function __construct(ApplicationContact $applicationContact, array $contactChoices = [])
    {
        $this->applicationContact = $applicationContact;

        $this->importToContactChoices = [
            ...$this->importToContactChoices,
            ...array_map(function (Contact $contact) {
                $this->importToContact = null;
                return $contact;
            }, $contactChoices)
        ];
    }

    public function getApplicationContact(): ApplicationContact
    {
        return $this->applicationContact;
    }

    public function getImportToContactChoices(): array
    {
        return $this->importToContactChoices;
    }

    /**
     * null    = no choice, invalid,
     * false   = don't do anything,
     * true    = create a new Contact based on $applicationContact,
     * Contact = update the given Contact with data from $applicationContact,
     *
     * @return null|bool|Contact
     */
    public function getImportToContact(): null|bool|Contact
    {
        return $this->importToContact;
    }

    public function setImportToContact(null|bool|Contact $importToContact): self
    {
        $this->importToContact = $importToContact;

        return $this;
    }
}