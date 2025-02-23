<?php

namespace Minuz\SkolieAPI\controllers;

use Minuz\SkolieAPI\http\Request;
use Minuz\SkolieAPI\http\Response;

class WrongRequestController
{
    public function index(Request $request, Response $response)
    {
        $response::Response(404, 'Wrong path or method, please try again', 'not found');
        return;
    }
}
