<?php

namespace App\Support;

use Illuminate\Http\Request;

class CanonicalUrl
{
    public static function fromRequest(Request $request, string $baseUrl): string
    {
        $query = array_filter($request->query(), function ($key) {
            $key = strtolower((string) $key);

            return ! str_starts_with($key, 'utm_') && ! in_array($key, [
                'gclid', 'dclid', 'fbclid', 'msclkid', 'gbraid', 'wbraid',
                'gad_source', 'gad_campaignid', 'srsltid', 'mc_cid', 'mc_eid',
            ], true);
        }, ARRAY_FILTER_USE_KEY);
        ksort($query);
        $queryString = http_build_query($query, '', '&', PHP_QUERY_RFC3986);

        return rtrim($baseUrl, '/').$request->getPathInfo().($queryString !== '' ? '?'.$queryString : '');
    }
}
