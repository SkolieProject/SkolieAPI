<?php

namespace Minuz\BaseApi\http;

class Request
{
    public static function auth(): array|false
    {
        if ( isset($_SERVER['PHP_AUTH_USER'], $_SERVER['PHP_AUTH_PW']) ) {
            $email = $_SERVER['PHP_AUTH_USER'];
            $password = $_SERVER['PHP_AUTH_PW'];

            return ['username' => $email, 'password' => $password];
        }
        return false;
    }



    public static function session(): string|bool
    {
        $headers = getallheaders();
        if ( ! isset($headers['Authorization']) ) {
            return false;
        }
        $authHeader = $headers['Authorization'];
        
        if ( 0 == preg_match('~Bearer\s(\S+)~', $authHeader, $matches) ) {
            return false;
        }
        
        $token = $matches[1];
        return $token;
    }



    public static function path(): string
    {
        return $_SERVER['REQUEST_URI'];
    }
    
    
    
    public static function method(): string
    {
        return $_SERVER['REQUEST_METHOD'];
    }



    public static function body(): array
    {
        return json_decode(file_get_contents('php://input'), true);
    }
}