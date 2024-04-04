<?php

namespace App\Model\Repository;

use App\Library\Data\Admin\ApplicationPaymentSearchData;
use App\Library\Search\Paginator\PaginatorInterface;
use App\Model\Entity\Application;
use App\Model\Entity\ApplicationPayment;
use Symfony\Component\Uid\UuidV4;

interface ApplicationPaymentRepositoryInterface
{
    /**
     * Saves an application payment.
     *
     * @param ApplicationPayment $applicationPayment
     * @param bool $flush
     * @return void
     */
    public function saveApplicationPayment(ApplicationPayment $applicationPayment, bool $flush): void;

    /**
     * Removes an application payment.
     *
     * @param ApplicationPayment $applicationPayment
     * @param bool $flush
     * @return void
     */
    public function removeApplicationPayment(ApplicationPayment $applicationPayment, bool $flush): void;

    /**
     * Finds one online payment by the given id.
     *
     * @param UuidV4 $id
     * @return ApplicationPayment|null
     */
    public function findOneById(UuidV4 $id): ?ApplicationPayment;

    /**
     * Finds one online payment by the given external id.
     *
     * @param string $externalId
     * @return ApplicationPayment|null
     */
    public function findOneByExternalId(string $externalId): ?ApplicationPayment;

    /**
     * Returns admin application payment search paginator.
     *
     * @param ApplicationPaymentSearchData $data
     * @param Application $application
     * @param int $currentPage
     * @param int $pageSize
     * @return PaginatorInterface
     */
    public function getAdminPaginator(ApplicationPaymentSearchData $data,
                                      Application                  $application,
                                      int                          $currentPage,
                                      int                          $pageSize): PaginatorInterface;
}