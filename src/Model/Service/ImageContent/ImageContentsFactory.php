<?php

namespace App\Model\Service\ImageContent;

use App\Model\Entity\ImageContent;
use App\Model\Repository\ImageContentRepositoryInterface;

/**
 * @inheritDoc
 */
class ImageContentsFactory implements ImageContentsFactoryInterface
{
    // private const DEFAULT_ALT = 'Lorem ipsum...';

    private ImageContentRepositoryInterface $imageContentRepository;
    
    public function __construct(ImageContentRepositoryInterface $imageContentRepository)
    {
        $this->imageContentRepository = $imageContentRepository;
    }

    /**
     * @inheritDoc
     */
    public function createImageContents(): array
    {
        // load existing
        $existingImageContents = [];

        foreach ($this->imageContentRepository->findAll() as $existingImageContent)
        {
            $existingImageContents[$existingImageContent->getIdentifier()] = $existingImageContent;
        }

        // create new
        $createdImageContents = [];
        $possibleImageContents = $this->instantiateImageContents();

        foreach ($possibleImageContents as $identifier => $textContent)
        {
            if (!array_key_exists($identifier, $existingImageContents))
            {
                $createdImageContents[] = $textContent;
            }
        }

        return $createdImageContents;
    }

    /**
     * @return ImageContent[]
     */
    private function instantiateImageContents(): array
    {
        /*$paymentMethods['test'] = new ImageContent('test', self::DEFAULT_ALT);*/

        return [];
    }
}