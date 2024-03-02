<?php

namespace App\Model\Service\User;

use App\Model\Entity\User;
use Symfony\Component\HttpFoundation\File\File;

/**
 * Helper service for purchasable item image files.
 */
interface UserImageFilesystemInterface
{
    /**
     * Gets the timestamp of last modification.
     *
     * @param User $user
     * @return int|null
     */
    public function getImageLastModified(User $user): ?int;

    /**
     * Returns true if the given URL is equal to the user image placeholder.
     *
     * @param string $publicUrl
     * @return bool
     */
    public function isUrlPlaceholder(string $publicUrl): bool;

    /**
     * Gets the public path to the given purchasable item image.
     *
     * @param User $user
     * @return string
     */
    public function getImagePublicUrl(User $user): string;

    /**
     * Uploads the given file and attaches it to the given purchasable item.
     *
     * @param File $file
     * @param User $user
     * @return void
     */
    public function uploadImageFile(File $file, User $user): void;

    /**
     * Removes the image file of the given purchasable item.
     *
     * @param User $user
     * @return void
     */
    public function removeImageFile(User $user): void;
}