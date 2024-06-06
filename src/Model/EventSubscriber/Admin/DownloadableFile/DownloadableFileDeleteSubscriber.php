<?php

namespace App\Model\EventSubscriber\Admin\DownloadableFile;

use App\Model\Event\Admin\DownloadableFile\DownloadableFileDeleteEvent;
use App\Model\Repository\DownloadableFileRepositoryInterface;
use App\Model\Service\DownloadableFile\DownloadableFileFilesystemInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

class DownloadableFileDeleteSubscriber
{
    private DownloadableFileFilesystemInterface $downloadableFileFilesystem;

    private DownloadableFileRepositoryInterface $downloadableFileRepository;

    public function __construct(DownloadableFileFilesystemInterface $downloadableFileFilesystem,
                                DownloadableFileRepositoryInterface $downloadableFileRepository)
    {
        $this->downloadableFileFilesystem = $downloadableFileFilesystem;
        $this->downloadableFileRepository = $downloadableFileRepository;
    }

    #[AsEventListener(event: DownloadableFileDeleteEvent::NAME, priority: 200)]
    public function onDeleteRemoveFile(DownloadableFileDeleteEvent $event): void
    {
        $entity = $event->getDownloadableFile();
        $this->downloadableFileFilesystem->removeFile($entity);
    }

    #[AsEventListener(event: DownloadableFileDeleteEvent::NAME, priority: 100)]
    public function onDeleteRemoveEntity(DownloadableFileDeleteEvent $event): void
    {
        $entity = $event->getDownloadableFile();
        $isFlush = $event->isFlush();
        $this->downloadableFileRepository->removeDownloadableFile($entity, $isFlush);
    }
}