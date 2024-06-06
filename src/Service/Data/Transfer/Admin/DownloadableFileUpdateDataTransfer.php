<?php

namespace App\Service\Data\Transfer\Admin;

use App\Library\Data\Admin\DownloadableFileUpdateData;
use App\Model\Entity\DownloadableFile;
use App\Service\Data\Transfer\DataTransferInterface;

/**
 * Transfers data from {@link DownloadableFileUpdateData} to {@link DownloadableFile} and vice versa.
 */
class DownloadableFileUpdateDataTransfer implements DataTransferInterface
{
    public function supports(object $data, object $entity): bool
    {
        return $data instanceof DownloadableFileUpdateData && $entity instanceof DownloadableFile;
    }

    public function fillData(object $data, object $entity): void
    {
        /** @var DownloadableFileUpdateData $downloadableFileData */
        /** @var DownloadableFile $downloadableFile */
        $downloadableFileData = $data;
        $downloadableFile = $entity;

        $downloadableFileData->setTitle($downloadableFile->getTitle());
        $downloadableFileData->setDescription($downloadableFile->getDescription());
        $downloadableFileData->setPriority($downloadableFile->getPriority());
    }

    public function fillEntity(object $data, object $entity): void
    {
        /** @var DownloadableFileUpdateData $downloadableFileData */
        /** @var DownloadableFile $downloadableFile */
        $downloadableFileData = $data;
        $downloadableFile = $entity;

        $downloadableFile->setTitle($downloadableFileData->getTitle());
        $downloadableFile->setDescription($downloadableFileData->getDescription());
        $downloadableFile->setPriority($downloadableFileData->getPriority());
    }
}