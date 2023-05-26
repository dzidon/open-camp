<?php

namespace App\Security\Exception;

use Symfony\Component\Security\Core\Exception\AuthenticationException;

/**
 * Thrown when a third-party provider does not find a user.
 */
class SocialUserNotFoundException extends AuthenticationException
{

}