<?php

namespace App\Model\Enum\Entity;

enum ApplicationPaymentTypeEnum: string
{
    case FULL = 'full';
    case DEPOSIT = 'deposit';
    case REST = 'rest';
}
