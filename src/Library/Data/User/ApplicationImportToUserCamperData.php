<?php

namespace App\Library\Data\User;

use App\Model\Entity\ApplicationCamper;
use App\Model\Entity\Camper;
use Symfony\Component\Validator\Constraints as Assert;

class ApplicationImportToUserCamperData
{
    private ApplicationCamper $applicationCamper;

    /** @var Camper[]|bool[] */
    private array $importToCamperChoices = [false, true];

    #[Assert\Choice(callback: 'getImportToCamperChoices')]
    #[Assert\NotNull]
    private null|bool|Camper $importToCamper = true;

    /**
     * @param ApplicationCamper $applicationCamper
     * @param Camper[] $CamperChoices
     */
    public function __construct(ApplicationCamper $applicationCamper, array $CamperChoices = [])
    {
        $this->applicationCamper = $applicationCamper;

        $this->importToCamperChoices = [
            ...$this->importToCamperChoices,
            ...array_map(function (Camper $camper) {
                $this->importToCamper = null;
                return $camper;
            }, $CamperChoices)
        ];
    }

    public function getApplicationCamper(): ApplicationCamper
    {
        return $this->applicationCamper;
    }

    public function getImportToCamperChoices(): array
    {
        return $this->importToCamperChoices;
    }

    /**
     * null   = no choice, invalid,
     * false  = don't do anything,
     * true   = create a new Camper based on $applicationCamper,
     * Camper = update the given Camper with data from $applicationCamper,
     *
     * @return null|bool|Camper
     */
    public function getImportToCamper(): null|bool|Camper
    {
        return $this->importToCamper;
    }

    public function setImportToCamper(null|bool|Camper $importToCamper): self
    {
        $this->importToCamper = $importToCamper;

        return $this;
    }
}