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

namespace OAuth2Framework\Component\Server\TokenTypeHint;

use OAuth2Framework\Component\Server\Model\AuthCode\AuthCode;
use OAuth2Framework\Component\Server\Model\AuthCode\AuthCodeId;
use OAuth2Framework\Component\Server\Model\AuthCode\AuthCodeRepositoryInterface;
use OAuth2Framework\Component\Server\Model\Token\Token;

final class AuthCodeTypeHint implements TokenTypeHintInterface
{
    /**
     * @var AuthCodeRepositoryInterface
     */
    private $authorizationCodeRepository;

    /**
     * AuthCode constructor.
     *
     * @param AuthCodeRepositoryInterface $authorizationCodeRepository
     */
    public function __construct(AuthCodeRepositoryInterface $authorizationCodeRepository)
    {
        $this->authorizationCodeRepository = $authorizationCodeRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function hint(): string
    {
        return 'auth_code';
    }

    /**
     * {@inheritdoc}
     */
    public function find(string $token)
    {
        $id = AuthCodeId::create($token);

        return $this->authorizationCodeRepository->find($id);
    }

    /**
     * {@inheritdoc}
     */
    public function revoke(Token $token)
    {
        if (!$token instanceof AuthCode || true === $token->isRevoked()) {
            return;
        }

        $token = $token->markAsRevoked();
        $this->authorizationCodeRepository->save($token);
    }

    /**
     * {@inheritdoc}
     */
    public function introspect(Token $token): array
    {
        if (!$token instanceof AuthCode || true === $token->isRevoked()) {
            return [
                'active' => false,
            ];
        }

        $result = [
            'active' => !$token->hasExpired(),
            'client_id' => $token->getClientId(),
            'exp' => $token->getExpiresAt()->getTimestamp(),
        ];

        if (!empty($token->getScopes())) {
            $result['scp'] = $token->getScopes();
        }

        return $result;
    }
}
