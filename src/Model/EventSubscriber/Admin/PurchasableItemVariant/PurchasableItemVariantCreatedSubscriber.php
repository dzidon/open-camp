<?php

namespace App\Model\EventSubscriber\Admin\PurchasableItemVariant;

use App\Model\Event\Admin\PurchasableItemVariant\PurchasableItemVariantCreatedEvent;
use App\Model\Repository\PurchasableItemVariantRepositoryInterface;
use App\Model\Repository\PurchasableItemVariantValueRepositoryInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

class PurchasableItemVariantCreatedSubscriber
{
    private PurchasableItemVariantRepositoryInterface $purchasableItemVariantRepository;

    private PurchasableItemVariantValueRepositoryInterface $purchasableItemVariantValueRepository;

    public function __construct(PurchasableItemVariantRepositoryInterface      $purchasableItemVariantRepository,
                                PurchasableItemVariantValueRepositoryInterface $purchasableItemVariantValueRepository)
    {
        $this->purchasableItemVariantRepository = $purchasableItemVariantRepository;
        $this->purchasableItemVariantValueRepository = $purchasableItemVariantValueRepository;
    }

    #[AsEventListener(event: PurchasableItemVariantCreatedEvent::NAME)]
    public function onCreatedSave(PurchasableItemVariantCreatedEvent $event): void
    {
        $purchasableItemVariant = $event->getPurchasableItemVariant();
        $purchasableItemVariantValues = $event->getPurchasableItemVariantValues();

        foreach ($purchasableItemVariantValues as $purchasableItemVariantValue)
        {
            $this->purchasableItemVariantValueRepository->savePurchasableItemVariantValue($purchasableItemVariantValue, false);
        }

        $this->purchasableItemVariantRepository->savePurchasableItemVariant($purchasableItemVariant, true);
    }
}