<?php

namespace App\Model\Enum\Entity;

/**
 * States of the UserPasswordChange entity.
 */
enum UserPasswordChangeStateEnum: string
{
    case USED = 'used';
    case UNUSED = 'unused';
    case DISABLED = 'disabled';
}
