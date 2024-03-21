<?php

namespace App\Model\Library\Camp;

use App\Library\Search\Paginator\PaginatorInterface;
use App\Model\Entity\Camp;

/**
 * Search result that contains camps and a number of pending applications for each camp.
 */
interface AdminApplicationCampsResultInterface
{
    /**
     * @return PaginatorInterface
     */
    public function getPaginator(): PaginatorInterface;

    /**
     * @param string|Camp $camp
     * @return int|null If null is returned, the number of pending applications is not known for the given camp.
     */
    public function getNumberOfPendingApplications(string|Camp $camp): ?int;
}