<?php

namespace App\Library\Data\Admin;

class GalleryImageCategoryTruncateData
{
    private bool $offspringsToo = false;

    public function offspringsToo(): bool
    {
        return $this->offspringsToo;
    }

    public function setOffspringsToo(bool $offspringsToo): self
    {
        $this->offspringsToo = $offspringsToo;

        return $this;
    }
}