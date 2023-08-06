<?php

namespace App\Model\Enum\Entity;

/**
 * Contact role (mother, father, ...).
 */
enum ContactRoleEnum: string
{
    case MOTHER = 'mother';
    case FATHER = 'father';
    case GRANDMA = 'grandma';
    case GRANDPA = 'grandpa';
    case AUNT = 'aunt';
    case UNCLE = 'uncle';
    case RELATIVE = 'relative';
    case TUTOR = 'tutor';
}
