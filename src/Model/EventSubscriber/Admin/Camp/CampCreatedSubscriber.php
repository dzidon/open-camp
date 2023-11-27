<?php

namespace App\Model\EventSubscriber\Admin\Camp;

use App\Model\Event\Admin\Camp\CampCreatedEvent;
use App\Model\Repository\CampImageRepositoryInterface;
use App\Model\Repository\CampRepositoryInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

class CampCreatedSubscriber
{
    private CampRepositoryInterface $campRepository;

    private CampImageRepositoryInterface $campImageRepository;

    public function __construct(CampRepositoryInterface      $campRepository,
                                CampImageRepositoryInterface $campImageRepository)
    {
        $this->campRepository = $campRepository;
        $this->campImageRepository = $campImageRepository;
    }

    #[AsEventListener(event: CampCreatedEvent::NAME)]
    public function onCreatedSave(CampCreatedEvent $event): void
    {
        $camp = $event->getCamp();
        $campImages = $event->getCampImages();

        foreach ($campImages as $campImage)
        {
            $this->campImageRepository->saveCampImage($campImage, false);
        }

        $this->campRepository->saveCamp($camp, true);
    }
}