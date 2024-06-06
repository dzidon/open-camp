<?php

namespace App\Model\Event\Admin\DownloadableFile;

use App\Library\Data\Admin\DownloadableFileUpdateData;
use App\Model\Entity\DownloadableFile;
use App\Model\Event\AbstractModelEvent;

class DownloadableFileUpdateEvent extends AbstractModelEvent
{
    public const NAME = 'model.admin.downloadable_file.update';

    private DownloadableFileUpdateData $data;

    private DownloadableFile $entity;

    public function __construct(DownloadableFileUpdateData $data, DownloadableFile $entity)
    {
        $this->data = $data;
        $this->entity = $entity;
    }

    public function getDownloadableFileUpdateData(): DownloadableFileUpdateData
    {
        return $this->data;
    }

    public function setDownloadableFileUpdateData(DownloadableFileUpdateData $data): self
    {
        $this->data = $data;

        return $this;
    }

    public function getDownloadableFile(): DownloadableFile
    {
        return $this->entity;
    }

    public function setDownloadableFile(DownloadableFile $entity): self
    {
        $this->entity = $entity;

        return $this;
    }
}