<?php

namespace App\Model\Service\CampDate;

use App\Model\Entity\CampDate;

/**
 * Outputs application summary in a file for the given camp date.
 */
interface CampDateApplicationSummaryExporterInterface
{
    /**
     * Returns file contents.
     *
     * @param CampDate $campDate
     * @return string
     */
    public function exportSummary(CampDate $campDate): string;

    /**
     * Specifies the file extension used by this exporter.
     *
     * @return string
     */
    public static function getFileExtension(): string;
}