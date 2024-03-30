<?php

namespace App\Library\Data\Common;

use App\Library\Constraint\DiscountSiblingIntervalInConfig;
use App\Model\Library\DiscountConfig\DiscountConfigArrayValidator;
use App\Model\Library\DiscountConfig\DiscountSiblingsIntervalShape;

#[DiscountSiblingIntervalInConfig]
class ApplicationDiscountsData
{
    private string $currency;

    private array $discountSiblingsConfig;

    private int $numberOfApplicationCampers;

    private false|array $discountSiblingsInterval = false;

    public function __construct(string $currency, array $discountSiblingsConfig, int $numberOfApplicationCampers)
    {
        $this->currency = $currency;
        $this->discountSiblingsConfig = $discountSiblingsConfig;
        $this->numberOfApplicationCampers = $numberOfApplicationCampers;

        DiscountConfigArrayValidator::assertSiblingsConfig($this->discountSiblingsConfig);
    }

    public function getCurrency(): string
    {
        return $this->currency;
    }

    public function getDiscountSiblingsConfig(): array
    {
        return $this->discountSiblingsConfig;
    }

    public function getNumberOfApplicationCampers(): int
    {
        return $this->numberOfApplicationCampers;
    }

    public function getDiscountSiblingsInterval(): false|array
    {
        return $this->discountSiblingsInterval;
    }

    public function setDiscountSiblingsInterval(false|array $discountSiblingsInterval): self
    {
        $this->discountSiblingsInterval = $discountSiblingsInterval;

        if ($this->discountSiblingsInterval !== false)
        {
            DiscountSiblingsIntervalShape::assertDiscountSiblingsInterval($this->discountSiblingsInterval);
        }

        return $this;
    }
}