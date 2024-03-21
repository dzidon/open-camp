<?php

namespace App\Library\Security\Authentication\Exception;

use Symfony\Component\Security\Core\Exception\AuthenticationException;

/**
 * Thrown when the email provided by a social login service is invalid.
 */
class InvalidSocialEmailException extends AuthenticationException
{

}