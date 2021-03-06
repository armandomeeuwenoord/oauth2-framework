<?php

declare(strict_types=1);

/*
 * The MIT License (MIT)
 *
 * Copyright (c) 2014-2018 Spomky-Labs
 *
 * This software may be modified and distributed under the terms
 * of the MIT license.  See the LICENSE file for details.
 */

namespace OAuth2Framework\Component\Core\AccessToken;

use OAuth2Framework\Component\Core\AccessToken\AccessToken;
use OAuth2Framework\Component\Core\AccessToken\AccessTokenId;
use OAuth2Framework\Component\Core\AccessToken\AccessTokenRepository as AccessTokenRepositoryInterface;

class AccessTokenHandlerManager
{
    /**
     * @var AccessTokenHandler[]
     */
    private $accessTokenHandlers = [];

    /**
     * @param AccessTokenHandler $accessTokenHandler
     *
     * @return AccessTokenHandlerManager
     */
    public function add(AccessTokenHandler $accessTokenHandler): self
    {
        $this->accessTokenHandlers[] = $accessTokenHandler;

        return $this;
    }

    /**
     * @param AccessTokenId $token
     *
     * @return null|AccessToken
     */
    public function find(AccessTokenId $token): ?AccessToken
    {
        foreach ($this->accessTokenHandlers as $accessTokenHandler) {
            $accessToken = $accessTokenHandler->find($token);
            if (null !== $accessToken) {
                return $accessToken;
            }
        }

        return null;
    }
}
