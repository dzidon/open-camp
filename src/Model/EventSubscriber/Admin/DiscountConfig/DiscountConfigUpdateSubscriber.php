<?php

namespace App\Model\EventSubscriber\Admin\DiscountConfig;

use App\Model\Event\Admin\DiscountConfig\DiscountConfigUpdateEvent;
use App\Model\Repository\DiscountConfigRepositoryInterface;
use App\Service\Data\Registry\DataTransferRegistryInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

class DiscountConfigUpdateSubscriber
{
    private DataTransferRegistryInterface $dataTransfer;

    private DiscountConfigRepositoryInterface $DiscountConfigRepository;

    public function __construct(DiscountConfigRepositoryInterface $DiscountConfigRepository,
                                DataTransferRegistryInterface       $dataTransfer)
    {
        $this->DiscountConfigRepository = $DiscountConfigRepository;
        $this->dataTransfer = $dataTransfer;
    }

    #[AsEventListener(event: DiscountConfigUpdateEvent::NAME, priority: 200)]
    public function onUpdateFillEntity(DiscountConfigUpdateEvent $event): void
    {
        $data = $event->getDiscountConfigData();
        $entity = $event->getDiscountConfig();
        $this->dataTransfer->fillEntity($data, $entity);
    }

    #[AsEventListener(event: DiscountConfigUpdateEvent::NAME, priority: 100)]
    public function onUpdateSaveEntities(DiscountConfigUpdateEvent $event): void
    {
        $DiscountConfig = $event->getDiscountConfig();
        $flush = $event->isFlush();
        $this->DiscountConfigRepository->saveDiscountConfig($DiscountConfig, $flush);
    }
}