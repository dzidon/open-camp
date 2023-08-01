<?php

namespace App\Enum\Entity;

/**
 * Contact role (mother, father, ...).
 */
enum ContactRoleEnum: string
{
    case MOTHER = 'mother';
    case FATHER = 'father';
    case TUTOR = 'tutor';
}
