<?php

namespace App\Model\Repository;

use App\Model\Entity\ApplicationAttachment;
use Symfony\Component\Uid\UuidV4;

interface ApplicationAttachmentRepositoryInterface
{
    /**
     * Saves an application attachment.
     *
     * @param ApplicationAttachment $applicationAttachment
     * @param bool $flush
     * @return void
     */
    public function saveApplicationAttachment(ApplicationAttachment $applicationAttachment, bool $flush): void;

    /**
     * Removes an application attachment.
     *
     * @param ApplicationAttachment $applicationAttachment
     * @param bool $flush
     * @return void
     */
    public function removeApplicationAttachment(ApplicationAttachment $applicationAttachment, bool $flush): void;

    /**
     * Finds one application attachment by id.
     *
     * @param UuidV4 $id
     * @return ApplicationAttachment|null
     */
    public function findOneById(UuidV4 $id): ?ApplicationAttachment;
}