<?php

namespace App\Model\EventSubscriber\Admin\ImageContent;

use App\Model\Event\Admin\ImageContent\ImageContentsCreateEvent;
use App\Model\Repository\ImageContentRepositoryInterface;
use App\Model\Service\ImageContent\ImageContentsFactoryInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

class ImageContentsCreateSubscriber
{
    private ImageContentsFactoryInterface $textContentsFactoryInterface;

    private ImageContentRepositoryInterface $textContentRepository;

    public function __construct(ImageContentsFactoryInterface   $textContentsFactoryInterface,
                                ImageContentRepositoryInterface $textContentRepository)
    {
        $this->textContentsFactoryInterface = $textContentsFactoryInterface;
        $this->textContentRepository = $textContentRepository;
    }

    #[AsEventListener(event: ImageContentsCreateEvent::NAME, priority: 200)]
    public function onCreateInstantiate(ImageContentsCreateEvent $event): void
    {
        $textContents = $this->textContentsFactoryInterface->createImageContents();
        $event->setImageContents($textContents);
    }

    #[AsEventListener(event: ImageContentsCreateEvent::NAME, priority: 100)]
    public function onCreateSavePermissionsAndGroups(ImageContentsCreateEvent $event): void
    {
        $textContents = $event->getImageContents();
        $isFlush = $event->isFlush();
        $keyLast = array_key_last($textContents);

        foreach ($textContents as $key => $textContent)
        {
            $isLast = $key === $keyLast;
            $this->textContentRepository->saveImageContent($textContent, $isFlush && $isLast);
        }
    }
}