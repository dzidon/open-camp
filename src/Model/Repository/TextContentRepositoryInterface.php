<?php

namespace App\Model\Repository;

use App\Model\Entity\TextContent;
use Symfony\Component\Uid\UuidV4;

interface TextContentRepositoryInterface
{
    /**
     * Saves text content.
     *
     * @param TextContent $textContent
     * @param bool $flush
     * @return void
     */
    public function saveTextContent(TextContent $textContent, bool $flush): void;

    /**
     * Removes text content.
     *
     * @param TextContent $textContent
     * @param bool $flush
     * @return void
     */
    public function removeTextContent(TextContent $textContent, bool $flush): void;

    /**
     * Finds all text contents.
     *
     * @return TextContent[]
     */
    public function findAll(): array;

    /**
     * Finds one text content by id.
     *
     * @param UuidV4 $id
     * @return TextContent|null
     */
    public function findOneById(UuidV4 $id): ?TextContent;

    /**
     * Finds one text content by identifier.
     *
     * @param string $identifier
     * @return TextContent|null
     */
    public function findOneByIdentifier(string $identifier): ?TextContent;
}