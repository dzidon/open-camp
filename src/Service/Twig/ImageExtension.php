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

    private string $publicFilePathPrefix;

    private string $companyLogoPath;

    public function __construct(CampImageFilesystemInterface            $campImageFilesystem,
                                PurchasableItemImageFilesystemInterface $purchasableItemImageFilesystem,
                                UserImageFilesystemInterface            $userImageFilesystem,
                                string                                  $publicFilePathPrefix,
                                string                                  $companyLogoPath)
    {
        $this->campImageFilesystem = $campImageFilesystem;
        $this->purchasableItemImageFilesystem = $purchasableItemImageFilesystem;
        $this->userImageFilesystem = $userImageFilesystem;
        $this->publicFilePathPrefix = $publicFilePathPrefix;
        $this->companyLogoPath = $companyLogoPath;
    }

    /**
     * @inheritDoc
     */
    public function getFunctions(): array
    {
        return [
            // getting public image urls
            new TwigFunction('company_logo_url', [$this, 'getCompanyLogoUrl']),
            new TwigFunction('camp_image_url', [$this, 'getCampImageUrl']),
            new TwigFunction('purchasable_item_image_url', [$this, 'getPurchasableItemImageUrl']),
            new TwigFunction('user_image_url', [$this, 'getUserImageUrl']),

            // checking if urls are placeholders
            new TwigFunction('is_camp_image_url_placeholder', [$this->campImageFilesystem, 'isUrlPlaceholder']),
            new TwigFunction('is_purchasable_item_image_url_placeholder', [$this->purchasableItemImageFilesystem, 'isUrlPlaceholder']),
            new TwigFunction('is_user_image_url_placeholder', [$this->userImageFilesystem, 'isUrlPlaceholder']),
        ];
    }

    public function getCompanyLogoUrl(bool $withPrefix = true): string
    {
        $url = '';

        if ($withPrefix)
        {
            $url .= $this->publicFilePathPrefix;
        }

        $url .= $this->companyLogoPath;

        return $url;
    }

    public function getCampImageUrl(?CampImage $campImage): string
    {
        $path = $this->campImageFilesystem->getImagePublicUrl($campImage);
        $time = null;

        if ($campImage !== null)
        {
            $time = $this->campImageFilesystem->getImageLastModified($campImage);
        }

        return $this->getUrlWithTime($path, $time);
    }

    public function getPurchasableItemImageUrl(PurchasableItem $purchasableItem): string
    {
        $path = $this->purchasableItemImageFilesystem->getImagePublicUrl($purchasableItem);
        $time = $this->purchasableItemImageFilesystem->getImageLastModified($purchasableItem);

        return $this->getUrlWithTime($path, $time);
    }

    public function getUserImageUrl(User $user): string
    {
        $path = $this->userImageFilesystem->getImagePublicUrl($user);
        $time = $this->userImageFilesystem->getImageLastModified($user);

        return $this->getUrlWithTime($path, $time);
    }

    private function getUrlWithTime(string $path, ?int $time): string
    {
        $url = $path;

        if ($time !== null)
        {
            $url .= '?t=' . $time;
        }

        return $url;
    }
}