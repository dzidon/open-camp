<?php

namespace App\Model\EventSubscriber\Admin\PurchasableItem;

use App\Model\Event\Admin\PurchasableItem\PurchasableItemCreateEvent;
use App\Model\Event\Admin\PurchasableItem\PurchasableItemUpdateEvent;
use App\Model\Service\PurchasableItem\PurchasableItemImageFilesystemInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

class PurchasableItemImageUploadSubscriber
{
    private PurchasableItemImageFilesystemInterface $purchasableItemImageFilesystem;

    public function __construct(PurchasableItemImageFilesystemInterface $purchasableItemImageFilesystem)
    {
        $this->purchasableItemImageFilesystem = $purchasableItemImageFilesystem;
    }

    #[AsEventListener(event: PurchasableItemCreateEvent::NAME, priority: 200)]
    #[AsEventListener(event: PurchasableItemUpdateEvent::NAME, priority: 200)]
    public function onCreateOrUpdateUploadImage(PurchasableItemCreateEvent|PurchasableItemUpdateEvent $event): void
    {
        $purchasableItem = $event->getPurchasableItem();
        $data = $event->getPurchasableItemData();
        $image = $data->getImage();
        $removeImage = $data->removeImage();

        if ($image !== null && !$removeImage)
        {
            $this->purchasableItemImageFilesystem->uploadImageFile($image, $purchasableItem);
        }

        if ($removeImage)
        {
            $this->purchasableItemImageFilesystem->removeImageFile($purchasableItem);
        }
    }
}