<?php

namespace App\Model\Event\Admin\Camp;

use App\Library\Data\Admin\CampCreationData;
use App\Model\Entity\Camp;
use App\Model\Entity\CampImage;
use LogicException;
use Symfony\Contracts\EventDispatcher\Event;

class CampCreatedEvent extends Event
{
    public const NAME = 'model.admin.camp.created';

    private CampCreationData $data;

    private Camp $entity;

    /** @var CampImage[] */
    private array $images;

    public function __construct(CampCreationData $data, Camp $entity, array $images)
    {
        foreach ($images as $image)
        {
            if (!$image instanceof CampImage)
            {
                throw new LogicException(
                    sprintf("Images passed to the constructor of %s must all be instances of %s.", self::class, CampImage::class)
                );
            }
        }

        $this->data = $data;
        $this->entity = $entity;
        $this->images = $images;
    }

    public function getCampCreationData(): CampCreationData
    {
        return $this->data;
    }

    public function setCampCreationData(CampCreationData $data): self
    {
        $this->data = $data;

        return $this;
    }

    public function getCamp(): Camp
    {
        return $this->entity;
    }

    public function setCamp(Camp $entity): self
    {
        $this->entity = $entity;

        return $this;
    }

    public function getCampImages(): array
    {
        return $this->images;
    }

    public function addCampImage(CampImage $campImage): self
    {
        if (in_array($campImage, $this->images, true))
        {
            return $this;
        }

        $this->images[] = $campImage;

        return $this;
    }

    public function removeCampImage(CampImage $campImage): self
    {
        $key = array_search($campImage, $this->images, true);

        if ($key === false)
        {
            return $this;
        }

        unset($this->images[$key]);

        return $this;
    }
}