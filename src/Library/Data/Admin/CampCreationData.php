<?php

namespace App\Library\Data\Admin;

use App\Library\Constraint\CampDateIntervals;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;

#[CampDateIntervals]
class CampCreationData
{
    #[Assert\Valid]
    private CampData $campData;

    /**
     * @var UploadedFile[] $images
     */
    #[Assert\All([
        new Assert\Image(),
    ])]
    private array $images = [];

    /**
     * @var CampDateData[]
     */
    #[Assert\Valid]
    private array $campDatesData = [];

    public function __construct()
    {
        $this->campData = new CampData();
    }

    public function getCampData(): CampData
    {
        return $this->campData;
    }

    public function getImages(): array
    {
        return $this->images;
    }

    public function setImages(array $images): self
    {
        $this->images = $images;

        return $this;
    }

    public function getCampDatesData(): array
    {
        return $this->campDatesData;
    }

    public function addCampDateData(CampDateData $campDateData): self
    {
        if (in_array($campDateData, $this->campDatesData, true))
        {
            return $this;
        }

        $this->campDatesData[] = $campDateData;

        return $this;
    }

    public function removeCampDateData(CampDateData $campDateData): self
    {
        $key = array_search($campDateData, $this->campDatesData, true);

        if ($key === false)
        {
            return $this;
        }

        unset($this->campDatesData[$key]);

        return $this;
    }
}