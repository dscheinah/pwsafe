<?php
namespace App;

/**
 * An exception to be thrown if the request tries to access a non public route without being authorized.
 *
 * @package App
 */
class AuthException extends \Exception
{
}
