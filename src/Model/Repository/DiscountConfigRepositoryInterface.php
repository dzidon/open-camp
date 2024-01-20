<?php

namespace App\Model\Repository;

use App\Library\Data\Admin\DiscountConfigSearchData;
use App\Library\Search\Paginator\PaginatorInterface;
use App\Model\Entity\DiscountConfig;
use Symfony\Component\Uid\UuidV4;

interface DiscountConfigRepositoryInterface
{
    /**
     * Saves a discount config.
     *
     * @param DiscountConfig $discountConfig
     * @param bool $flush
     * @return void
     */
    public function saveDiscountConfig(DiscountConfig $discountConfig, bool $flush): void;

    /**
     * Removes a discount config.
     *
     * @param DiscountConfig $discountConfig
     * @param bool $flush
     * @return void
     */
    public function removeDiscountConfig(DiscountConfig $discountConfig, bool $flush): void;

    /**
     * Finds all available discount configs.
     *
     * @return DiscountConfig[]
     */
    public function findAll(): array;

    /**
     * Finds one discount config by id.
     *
     * @param UuidV4 $id
     * @return DiscountConfig|null
     */
    public function findOneById(UuidV4 $id): ?DiscountConfig;

    /**
     * Finds one discount config by name.
     *
     * @param string $name
     * @return DiscountConfig|null
     */
    public function findOneByName(string $name): ?DiscountConfig;

    /**
     * Returns admin discount config search paginator.
     *
     * @param DiscountConfigSearchData $data
     * @param int $currentPage
     * @param int $pageSize
     * @return PaginatorInterface
     */
    public function getAdminPaginator(DiscountConfigSearchData $data, int $currentPage, int $pageSize): PaginatorInterface;
}