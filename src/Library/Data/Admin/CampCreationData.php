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
    private iterable $images = [];

    /**
     * @var CampDateData[]
     */
    #[Assert\Valid]
    private iterable $campDatesData = [];

    public function __construct()
    {
        $this->campData = new CampData();
    }

    public function getCampData(): CampData
    {
        return $this->campData;
    }

    public function getImages(): iterable
    {
        return $this->images;
    }

    public function setImages(iterable $images): self
    {
        $this->images = $images;

        return $this;
    }

    public function getCampDatesData(): iterable
    {
        return $this->campDatesData;
    }

    public function setCampDatesData(iterable $campDatesData): self
    {
        $this->campDatesData = $campDatesData;

        return $this;
    }
}