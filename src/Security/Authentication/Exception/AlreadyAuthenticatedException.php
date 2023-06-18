<?php

namespace App\Security\Authentication\Exception;

use Symfony\Component\Security\Core\Exception\AuthenticationException;

/**
 * Thrown when the user is already logged in.
 */
class AlreadyAuthenticatedException extends AuthenticationException
{

}