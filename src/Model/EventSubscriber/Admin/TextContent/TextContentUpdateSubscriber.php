<?php

namespace App\Model\EventSubscriber\Admin\TextContent;

use App\Model\Event\Admin\TextContent\TextContentUpdateEvent;
use App\Model\Repository\TextContentRepositoryInterface;
use App\Service\Data\Registry\DataTransferRegistryInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

class TextContentUpdateSubscriber
{
    private TextContentRepositoryInterface $repository;

    private DataTransferRegistryInterface $dataTransfer;

    public function __construct(TextContentRepositoryInterface $repository,
                                DataTransferRegistryInterface       $dataTransfer)
    {
        $this->repository = $repository;
        $this->dataTransfer = $dataTransfer;
    }

    #[AsEventListener(event: TextContentUpdateEvent::NAME, priority: 200)]
    public function onUpdateFillEntity(TextContentUpdateEvent $event): void
    {
        $data = $event->getTextContentData();
        $entity = $event->getTextContent();
        $this->dataTransfer->fillEntity($data, $entity);
    }

    #[AsEventListener(event: TextContentUpdateEvent::NAME, priority: 100)]
    public function onUpdateSaveEntity(TextContentUpdateEvent $event): void
    {
        $entity = $event->getTextContent();
        $isFlush = $event->isFlush();
        $this->repository->saveTextContent($entity, $isFlush);
    }
}