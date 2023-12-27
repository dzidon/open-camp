<?php

namespace App\Model\EventSubscriber\Admin\AttachmentConfig;

use App\Model\Event\Admin\AttachmentConfig\AttachmentConfigUpdateEvent;
use App\Model\Repository\AttachmentConfigRepositoryInterface;
use App\Service\Data\Registry\DataTransferRegistryInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

class AttachmentConfigUpdateSubscriber
{
    private DataTransferRegistryInterface $dataTransfer;

    private AttachmentConfigRepositoryInterface $attachmentConfigRepository;

    public function __construct(AttachmentConfigRepositoryInterface $attachmentConfigRepository,
                                DataTransferRegistryInterface       $dataTransfer)
    {
        $this->attachmentConfigRepository = $attachmentConfigRepository;
        $this->dataTransfer = $dataTransfer;
    }

    #[AsEventListener(event: AttachmentConfigUpdateEvent::NAME, priority: 200)]
    public function onUpdateFillEntity(AttachmentConfigUpdateEvent $event): void
    {
        $data = $event->getAttachmentConfigData();
        $entity = $event->getAttachmentConfig();
        $this->dataTransfer->fillEntity($data, $entity);
    }

    #[AsEventListener(event: AttachmentConfigUpdateEvent::NAME, priority: 100)]
    public function onUpdateSaveEntities(AttachmentConfigUpdateEvent $event): void
    {
        $attachmentConfig = $event->getAttachmentConfig();
        $flush = $event->isFlush();
        $this->attachmentConfigRepository->saveAttachmentConfig($attachmentConfig, $flush);
    }
}