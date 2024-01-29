<?php

namespace App\Service\Twig;

use App\Model\Entity\CampImage;
use App\Model\Entity\PurchasableItem;
use App\Model\Entity\User;
use App\Model\Service\CampImage\CampImageFilesystemInterface;
use App\Model\Service\PurchasableItem\PurchasableItemImageFilesystemInterface;
use App\Model\Service\User\UserImageFilesystemInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * Adds image related functions to Twig.
 */
class ImageExtension extends AbstractExtension
{
    private CampImageFilesystemInterface $campImageFilesystem;
    private PurchasableItemImageFilesystemInterface $purchasableItemImageFilesystem;
    private UserImageFilesystemInterface $userImageFilesystem;

    public function __construct(CampImageFilesystemInterface            $campImageFilesystem,
                                PurchasableItemImageFilesystemInterface $purchasableItemImageFilesystem,
                                UserImageFilesystemInterface            $userImageFilesystem)
    {
        $this->campImageFilesystem = $campImageFilesystem;
        $this->purchasableItemImageFilesystem = $purchasableItemImageFilesystem;
        $this->userImageFilesystem = $userImageFilesystem;
    }

    /**
     * @inheritDoc
     */
    public function getFunctions(): array
    {
        return [
            new TwigFunction('camp_image_path', [$this, 'getCampImagePath']),
            new TwigFunction('purchasable_item_image_path', [$this, 'getPurchasableItemImagePath']),
            new TwigFunction('user_image_path', [$this, 'getUserImagePath']),
        ];
    }

    public function getCampImagePath(?CampImage $campImage): string
    {
        $path = $this->campImageFilesystem->getFilePath($campImage);

        return $this->getPathWithTime($path);
    }

    public function getPurchasableItemImagePath(PurchasableItem $purchasableItem): string
    {
        $path = $this->purchasableItemImageFilesystem->getImageFilePath($purchasableItem);

        return $this->getPathWithTime($path);
    }

    public function getUserImagePath(User $user): string
    {
        $path = $this->userImageFilesystem->getImageFilePath($user);

        return $this->getPathWithTime($path);
    }

    private function getPathWithTime(string $path): string
    {
        $time = file_exists($path) ? filemtime($path) : 0;

        return $path . '?t=' . $time;
    }
}