<?php

namespace App\Enum\Entity;

/**
 * States of the UserPasswordChange entity.
 */
enum UserPasswordChangeStateEnum: string
{
    case USED = 'used';
    case UNUSED = 'unused';
    case DISABLED = 'disabled';
}
