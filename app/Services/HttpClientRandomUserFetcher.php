<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Kerby\EonxTestTask\DataProvider\RandomUser\RandomUserSimpleFetcher;

class HttpClientRandomUserFetcher extends RandomUserSimpleFetcher
{
    /**
     * @codeCoverageIgnore We aren't performing low level requests while testing so just ignore this method
     *
     * @param string $baseUri
     * @param array $params
     * @return string
     */
    protected function getApiTextResponse(string $baseUri, array $params = []): string
    {
        return Http::get($baseUri, $params)->body();
    }
}
