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

namespace OAuth2Framework\Component\RefreshTokenGrant\Command;

use OAuth2Framework\Component\RefreshTokenGrant\RefreshTokenRepository;

class AddAccessTokenCommandHandler
{
    /**
     * @var RefreshTokenRepository
     */
    private $refreshTokenRepository;

    /**
     * CreateClientCommandHandler constructor.
     *
     * @param RefreshTokenRepository $refreshTokenRepository
     */
    public function __construct(RefreshTokenRepository $refreshTokenRepository)
    {
        $this->refreshTokenRepository = $refreshTokenRepository;
    }

    /**
     * @param AddAccessTokenCommand $command
     */
    public function handle(AddAccessTokenCommand $command)
    {
        $refreshTokenId = $command->getRefreshTokenId();
        $refreshToken = $this->refreshTokenRepository->find($refreshTokenId);
        if (null === $refreshToken) {
            throw new \InvalidArgumentException(sprintf('Unable to find the refresh token with ID "%s".', $command->getRefreshTokenId()->getValue()));
        }
        $refreshToken = $refreshToken->addAccessToken($command->getAccessTokenId());
        $this->refreshTokenRepository->save($refreshToken);
    }
}
