<?php

namespace App\Model\Event\Admin\DownloadableFile;

use App\Model\Entity\DownloadableFile;
use App\Model\Event\AbstractModelEvent;

class DownloadableFileDeleteEvent extends AbstractModelEvent
{
    public const NAME = 'model.admin.downloadable_file.delete';

    private DownloadableFile $entity;

    public function __construct(DownloadableFile $entity)
    {
        $this->entity = $entity;
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