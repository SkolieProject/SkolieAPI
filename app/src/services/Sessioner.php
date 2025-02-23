<?php

namespace Minuz\SkolieAPI\services;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Minuz\SkolieAPI\config\CacheTimer\CacheExpires;
use UnexpectedValueException;

session_start();
class Sessioner
{
    public static function saveSession(array|bool $auth): string
    {
        $token = JWT::encode($auth, $_ENV['JWT_KEY'], 'HS256');
        $_SESSION['token'] = $token;
        $_SESSION['last_activity'] = time();
        return $token;
    }



    public static function assertSession(string $session_token): \stdClass|false
    {
        $inactive = time() - $_SESSION['last_activity'];
        if ($inactive >= CacheExpires::slow) {
            unset($_SESSION['last_activity'], $_SESSION['token']);
            return false;
        }
        if ($session_token != $_SESSION['token']) {
            return false;
        }

        try {
            $credentials = JWT::decode($_SESSION['token'], new Key($_ENV['JWT_KEY'], 'HS256'));
            return $credentials;
        } catch (UnexpectedValueException $e) {
            return false;
        }
    }
}
