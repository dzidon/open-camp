<?php

namespace App\Library\Enum\Search\Data\Admin;

use App\Library\Enum\Search\Data\SortEnumTrait;

/**
 * Admin application payment sort cases.
 */
enum ApplicationPaymentSortEnum: string
{
    use SortEnumTrait;

    case CREATED_AT_DESC = 'applicationPayment.createdAt DESC';
    case CREATED_AT_ASC = 'applicationPayment.createdAt ASC';
    case AMOUNT_ASC = 'applicationPayment.amount ASC';
    case AMOUNT_DESC = 'applicationPayment.amount DESC';
}
