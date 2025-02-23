<?php

namespace Minuz\SkolieAPI\Tools;

use Minuz\SkolieAPI\tools\Parser;


class URLDecomposer
{
    public static function Detach(string $url, array &$url_info = null): void
    {
        $router_path = $url;
        $url_info = [
            'id' => false,
            'query' => false
        ];
        $query_string = parse_url($url, PHP_URL_QUERY);

        $request_path = parse_url($url, PHP_URL_PATH);
        $pattern_id = '~\/[\w]+\/([\S]+)~';
        if (preg_match($pattern_id, $request_path, $matches)) {

            $id = $matches[1];


            $router_path = str_replace($id, '{id}', $router_path);
            $url_info['id'] = $id;
        }

        if (! empty($query_string)) {
            parse_str($query_string, $query);
            Parser::HydrateNulls($query, false);
            $router_path = str_replace($query_string, '{query}', $router_path);
            $url_info['query'] = $query;
        }

        $url_info['path'] = $router_path;
    }
}
