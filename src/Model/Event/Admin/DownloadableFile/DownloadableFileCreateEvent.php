<?php

namespace App\Model\Event\Admin\DownloadableFile;

use App\Library\Data\Admin\DownloadableFileCreateData;
use App\Model\Entity\DownloadableFile;
use App\Model\Event\AbstractModelEvent;

class DownloadableFileCreateEvent extends AbstractModelEvent
{
    public const NAME = 'model.admin.downloadable_file.create';

    private DownloadableFileCreateData $data;

    private ?DownloadableFile $downloadableFile = null;

    public function __construct(DownloadableFileCreateData $data)
    {
        $this->data = $data;
    }

    public function getDownloadableFileCreateData(): DownloadableFileCreateData
    {
        return $this->data;
    }

    public function setDownloadableFileCreateData(DownloadableFileCreateData $data): self
    {
        $this->data = $data;

        return $this;
    }

    public function getDownloadableFile(): ?DownloadableFile
    {
        return $this->downloadableFile;
    }

    public function setDownloadableFile(?DownloadableFile $downloadableFile): self
    {
        $this->downloadableFile = $downloadableFile;

        return $this;
    }
}