<?php
namespace Minuz\BaseApi\services;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Minuz\BaseApi\config\CacheTimer\CacheExpires;
use UnexpectedValueException;

session_start();
class Sessioner
{
    public static function saveSession(array|bool $auth): string
    {
        $token = JWT::encode($auth, $_ENV['JWT_KEY'], 'HS256');
        $_SESSION['authData'] = $token;
        $_SESSION['last_activity'] = time();
        return $token;
    }
    


    public static function assertSession(string $userToken): \stdClass|false
    {
        $inactive = time() - $_SESSION['last_activity'];
        if ( $inactive >=CacheExpires::slow ) {
            unset($_SESSION['last_activity'], $_SESSION['authData']);
            return false;
        }
        if ( $userToken != $_SESSION['authData'] ) {
            return false;
        }
        try {
            $credentials = JWT::decode($_SESSION['authData'], new Key($_ENV['JWT_KEY'], 'HS256'));
            return $credentials;
        } catch (UnexpectedValueException $e) {
            return false;
        }
    }
}