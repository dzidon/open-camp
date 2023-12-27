<?php

namespace App\Model\Repository;

use App\Model\Entity\CampDate;
use App\Model\Entity\CampDateAttachmentConfig;

interface CampDateAttachmentConfigRepositoryInterface
{
    /**
     * Saves a camp date attachment config.
     *
     * @param CampDateAttachmentConfig $campDateAttachmentConfig
     * @param bool $flush
     * @return void
     */
    public function saveCampDateAttachmentConfig(CampDateAttachmentConfig $campDateAttachmentConfig, bool $flush): void;

    /**
     * Removes a camp date attachment config.
     *
     * @param CampDateAttachmentConfig $campDateAttachmentConfig
     * @param bool $flush
     * @return void
     */
    public function removeCampDateAttachmentConfig(CampDateAttachmentConfig $campDateAttachmentConfig, bool $flush): void;

    /**
     * Finds all camp date attachment configs that have the given camp date.
     *
     * @param CampDate $campDate
     * @param null|bool $isGlobal
     * @return CampDateAttachmentConfig[]
     */
    public function findByCampDate(CampDate $campDate, ?bool $isGlobal = null): array;
}