<?php

namespace App\Model\EventSubscriber\Admin\DiscountConfig;

use App\Model\Entity\DiscountConfig;
use App\Model\Event\Admin\DiscountConfig\DiscountConfigCreateEvent;
use App\Model\Repository\DiscountConfigRepositoryInterface;
use App\Service\Data\Registry\DataTransferRegistryInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

class DiscountConfigCreateSubscriber
{
    private DataTransferRegistryInterface $dataTransfer;

    private DiscountConfigRepositoryInterface $DiscountConfigRepository;

    public function __construct(DataTransferRegistryInterface $dataTransfer, DiscountConfigRepositoryInterface $DiscountConfigRepository)
    {
        $this->dataTransfer = $dataTransfer;
        $this->DiscountConfigRepository = $DiscountConfigRepository;
    }

    #[AsEventListener(event: DiscountConfigCreateEvent::NAME, priority: 200)]
    public function onCreateFillEntity(DiscountConfigCreateEvent $event): void
    {
        $data = $event->getDiscountConfigData();
        $DiscountConfig = new DiscountConfig($data->getName());
        $this->dataTransfer->fillEntity($data, $DiscountConfig);
        $event->setDiscountConfig($DiscountConfig);
    }

    #[AsEventListener(event: DiscountConfigCreateEvent::NAME, priority: 100)]
    public function onCreateSaveEntities(DiscountConfigCreateEvent $event): void
    {
        $DiscountConfig = $event->getDiscountConfig();
        $flush = $event->isFlush();
        $this->DiscountConfigRepository->saveDiscountConfig($DiscountConfig, $flush);
    }
}