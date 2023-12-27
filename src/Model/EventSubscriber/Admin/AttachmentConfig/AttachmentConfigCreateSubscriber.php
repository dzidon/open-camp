<?php

namespace App\Model\EventSubscriber\Admin\AttachmentConfig;

use App\Model\Entity\AttachmentConfig;
use App\Model\Event\Admin\AttachmentConfig\AttachmentConfigCreateEvent;
use App\Model\Repository\AttachmentConfigRepositoryInterface;
use App\Service\Data\Registry\DataTransferRegistryInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

class AttachmentConfigCreateSubscriber
{
    private DataTransferRegistryInterface $dataTransfer;

    private AttachmentConfigRepositoryInterface $attachmentConfigRepository;

    public function __construct(DataTransferRegistryInterface $dataTransfer, AttachmentConfigRepositoryInterface $attachmentConfigRepository)
    {
        $this->dataTransfer = $dataTransfer;
        $this->attachmentConfigRepository = $attachmentConfigRepository;
    }

    #[AsEventListener(event: AttachmentConfigCreateEvent::NAME, priority: 200)]
    public function onCreateFillEntity(AttachmentConfigCreateEvent $event): void
    {
        $data = $event->getAttachmentConfigData();
        $attachmentConfig = new AttachmentConfig($data->getName(), $data->getLabel(), $data->getMaxSize());
        $this->dataTransfer->fillEntity($data, $attachmentConfig);
        $event->setAttachmentConfig($attachmentConfig);
    }

    #[AsEventListener(event: AttachmentConfigCreateEvent::NAME, priority: 100)]
    public function onCreateSaveEntities(AttachmentConfigCreateEvent $event): void
    {
        $attachmentConfig = $event->getAttachmentConfig();
        $flush = $event->isFlush();
        $this->attachmentConfigRepository->saveAttachmentConfig($attachmentConfig, $flush);
    }
}