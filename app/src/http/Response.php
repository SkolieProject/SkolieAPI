<?php

namespace Minuz\SkolieAPI\http;


class Response
{
    public static function Response(int $code, string $warning = 'None', string $message = 'None', array $data = [], ?string $jwt = null)
    {
        header('Content-type: application/json', response_code: $code);
        header("Access-Control-Allow-Origin: *");


        if ($jwt != null) {
            header("Authorization: Bearer $jwt");
        }

        $data = array_merge(
            ['Warning' => $warning, 'Status message' => $message],
            $data
        );

        $json = json_encode($data);

        echo $json;
    }
}
