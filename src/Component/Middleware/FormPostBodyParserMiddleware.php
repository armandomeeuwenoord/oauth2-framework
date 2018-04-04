<?php

declare(strict_types=1);

/*
 * The MIT License (MIT)
 *
 * Copyright (c) 2014-2017 Spomky-Labs
 *
 * This software may be modified and distributed under the terms
 * of the MIT license.  See the LICENSE file for details.
 */

namespace OAuth2Framework\Component\Middleware;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Message\ResponseInterface;
//use OAuth2Framework\Component\Middleware\RequestHandlerInterface;
use Psr\Http\Server\RequestHandlerInterface;

final class FormPostBodyParserMiddleware implements MiddlewareInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $headers = $request->getHeader('content-type');
        foreach ($headers as $header) {
            if ('application/x-www-form-urlencoded' === substr($header, 0, 33)) {
                $request->getBody()->rewind();
                $body = $request->getBody()->getContents();
                if (true === parse_str($body, $parsed)) {
                    $request = $request->withParsedBody($parsed);
                }
            }
        }

        return $handler->handle($request);
    }
}