<?php

namespace Minuz\SkolieAPI\controllers;

use Minuz\SkolieAPI\attributes\Route;
use Minuz\SkolieAPI\http\Request;
use Minuz\SkolieAPI\http\Response;
use Minuz\SkolieAPI\repo\AccountRepository;
use Minuz\SkolieAPI\services\Sessioner;

class EntranceController
{
    private AccountRepository $account_repository;

    public function __construct()
    {
        $this->account_repository = new AccountRepository();
    }


    #[Route('/login/', 'GET')]
    public function login(Request $request, Response $response): void
    {
        $user_auth = $request::auth();

        $repository = $this->account_repository->enter($user_auth['username'], $user_auth['password']);
        if ($repository->exit_code) {
            $response::Response(400, 'Error', 'Email or password incorrect');
            return;
        }

        $acc = $repository->account;

        $acess_token = Sessioner::saveSession([...$user_auth, 'id' => $acc->id]);
        $response::Response(200, 'OK', 'You have loged in sucessfully', ['userinfo' => $acc->overview()], jwt: $acess_token);
        return;
    }
}
