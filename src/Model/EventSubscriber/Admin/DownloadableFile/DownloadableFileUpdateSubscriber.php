<?php

namespace App\Model\EventSubscriber\Admin\DownloadableFile;

use App\Model\Event\Admin\DownloadableFile\DownloadableFileUpdateEvent;
use App\Model\Repository\DownloadableFileRepositoryInterface;
use App\Service\Data\Registry\DataTransferRegistryInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

class DownloadableFileUpdateSubscriber
{
    private DownloadableFileRepositoryInterface $repository;

    private DataTransferRegistryInterface $dataTransfer;

    public function __construct(DownloadableFileRepositoryInterface $repository,
                                DataTransferRegistryInterface       $dataTransfer)
    {
        $this->repository = $repository;
        $this->dataTransfer = $dataTransfer;
    }

    #[AsEventListener(event: DownloadableFileUpdateEvent::NAME, priority: 200)]
    public function onUpdateFillEntity(DownloadableFileUpdateEvent $event): void
    {
        $data = $event->getDownloadableFileUpdateData();
        $entity = $event->getDownloadableFile();
        $this->dataTransfer->fillEntity($data, $entity);
    }

    #[AsEventListener(event: DownloadableFileUpdateEvent::NAME, priority: 100)]
    public function onUpdateSaveEntity(DownloadableFileUpdateEvent $event): void
    {
        $entity = $event->getDownloadableFile();
        $isFlush = $event->isFlush();
        $this->repository->saveDownloadableFile($entity, $isFlush);
    }
}