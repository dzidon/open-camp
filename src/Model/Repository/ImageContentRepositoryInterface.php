<?php

namespace App\Model\Repository;

use App\Model\Entity\ImageContent;
use Symfony\Component\Uid\UuidV4;

interface ImageContentRepositoryInterface
{
    /**
     * Saves image content.
     *
     * @param ImageContent $imageContent
     * @param bool $flush
     * @return void
     */
    public function saveImageContent(ImageContent $imageContent, bool $flush): void;

    /**
     * Removes image content.
     *
     * @param ImageContent $imageContent
     * @param bool $flush
     * @return void
     */
    public function removeImageContent(ImageContent $imageContent, bool $flush): void;

    /**
     * Finds all image contents.
     *
     * @return ImageContent[]
     */
    public function findAll(): array;

    /**
     * Finds one image content by id.
     *
     * @param UuidV4 $id
     * @return ImageContent|null
     */
    public function findOneById(UuidV4 $id): ?ImageContent;

    /**
     * Finds one image content by identifier.
     *
     * @param string $identifier
     * @return ImageContent|null
     */
    public function findOneByIdentifier(string $identifier): ?ImageContent;
}