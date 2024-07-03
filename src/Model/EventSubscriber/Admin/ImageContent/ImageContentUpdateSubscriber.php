<?php

namespace App\Model\EventSubscriber\Admin\ImageContent;

use App\Model\Event\Admin\ImageContent\ImageContentUpdateEvent;
use App\Model\Repository\ImageContentRepositoryInterface;
use App\Service\Data\Registry\DataTransferRegistryInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

class ImageContentUpdateSubscriber
{
    private ImageContentRepositoryInterface $repository;

    private DataTransferRegistryInterface $dataTransfer;

    public function __construct(ImageContentRepositoryInterface $repository,
                                DataTransferRegistryInterface   $dataTransfer)
    {
        $this->repository = $repository;
        $this->dataTransfer = $dataTransfer;
    }

    #[AsEventListener(event: ImageContentUpdateEvent::NAME, priority: 200)]
    public function onUpdateFillEntity(ImageContentUpdateEvent $event): void
    {
        $data = $event->getImageContentData();
        $entity = $event->getImageContent();
        $this->dataTransfer->fillEntity($data, $entity);
    }

    #[AsEventListener(event: ImageContentUpdateEvent::NAME, priority: 100)]
    public function onUpdateSaveEntity(ImageContentUpdateEvent $event): void
    {
        $entity = $event->getImageContent();
        $isFlush = $event->isFlush();
        $this->repository->saveImageContent($entity, $isFlush);
    }
}