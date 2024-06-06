<?php

namespace App\Model\EventSubscriber\Admin\DownloadableFile;

use App\Model\Event\Admin\DownloadableFile\DownloadableFileCreateEvent;
use App\Model\Repository\DownloadableFileRepositoryInterface;
use App\Model\Service\DownloadableFile\DownloadableFileFactoryInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

class DownloadableFileCreateSubscriber
{
    private DownloadableFileRepositoryInterface $downloadableFileRepository;

    private DownloadableFileFactoryInterface $downloadableFileFactory;

    public function __construct(DownloadableFileRepositoryInterface $downloadableFileRepository,
                                DownloadableFileFactoryInterface    $downloadableFileFactory)
    {
        $this->downloadableFileRepository = $downloadableFileRepository;
        $this->downloadableFileFactory = $downloadableFileFactory;
    }

    #[AsEventListener(event: DownloadableFileCreateEvent::NAME, priority: 200)]
    public function onCreateInstantiateEntity(DownloadableFileCreateEvent $event): void
    {
        $data = $event->getDownloadableFileCreateData();
        $title = $data->getTitle();
        $priority = $data->getPriority();
        $description = $data->getDescription();
        $file = $data->getFile();

        $downloadableFile = $this->downloadableFileFactory->createDownloadableFile(
            $file,
            $title,
            $priority
        );

        $downloadableFile->setDescription($description);
        $event->setDownloadableFile($downloadableFile);
    }

    #[AsEventListener(event: DownloadableFileCreateEvent::NAME, priority: 100)]
    public function onCreateSaveEntity(DownloadableFileCreateEvent $event): void
    {
        $entity = $event->getDownloadableFile();
        $isFlush = $event->isFlush();
        $this->downloadableFileRepository->saveDownloadableFile($entity, $isFlush);
    }
}