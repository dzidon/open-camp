<?php

namespace App\Security\Exception;

use Symfony\Component\Security\Core\Exception\AuthenticationException;

/**
 * Thrown when the user is already logged in.
 */
class AlreadyAuthenticatedException extends AuthenticationException
{

}