<?php
namespace Minuz\BaseApi\controllers;

use Minuz\BaseApi\attributes\Route;
use Minuz\BaseApi\http\Request;
use Minuz\BaseApi\http\Response;
use Minuz\BaseApi\services\Sessioner;

class LoginExampleController
{
    #[Route('/login', 'GET')]
    public function login(Request $request, Response $response): void
    {
        $userLogin = $request::auth();
        $acessToken = Sessioner::saveSession($userLogin);

        $response::Response(200, 'OK', 'You have loged in sucessfully', jwt: $acessToken);
    }


    #[Route('/acessExample')]
    public function acessExample(Request $request, Response $response): void
    {
        $userToken = $request::session();
        $credentials = Sessioner::assertSession($userToken);
        if ( $credentials == false ) {
            $response::Response(400, 'Error', 'Your login has failed');
            return;
        }

        // Code to enter the account

        $response::Response(200, 'OK', 'Acess completed', ['credentials' => $credentials]);
        return;
    }
}