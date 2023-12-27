<?php

namespace App\Library\Data\Admin;

use App\Model\Entity\Camp;
use LogicException;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Validator\Constraints as Assert;

class CampImagesUploadData
{
    /**
     * @var File[] $images
     */
    #[Assert\All([
        new Assert\Image(),
    ])]
    #[Assert\NotBlank]
    private array $images = [];

    private Camp $camp;

    public function __construct(Camp $camp)
    {
        $this->camp = $camp;
    }

    public function getCamp(): Camp
    {
        return $this->camp;
    }

    public function getImages(): array
    {
        return $this->images;
    }

    public function setImages(array $images): self
    {
        foreach ($images as $image)
        {
            if (!$image instanceof File)
            {
                throw new LogicException(
                    sprintf('Array passed to %s must only contain instances of %s.', __METHOD__, File::class)
                );
            }
        }

        $this->images = $images;

        return $this;
    }
}