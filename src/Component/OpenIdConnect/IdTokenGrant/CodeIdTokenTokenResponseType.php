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

namespace OAuth2Framework\Component\OpenIdConnect\IdTokenGrant;

use OAuth2Framework\Component\AuthorizationCodeGrant\AuthorizationCodeResponseType;
use OAuth2Framework\Component\AuthorizationEndpoint\Authorization;
use OAuth2Framework\Component\AuthorizationEndpoint\ResponseType;
use OAuth2Framework\Component\ImplicitGrant\TokenResponseType;

class CodeIdTokenTokenResponseType implements ResponseType
{
    /**
     * @var AuthorizationCodeResponseType
     */
    private $codeResponseType;

    /**
     * @var IdTokenResponseType
     */
    private $idTokenResponseType;

    /**
     * @var TokenResponseType
     */
    private $tokenResponseType;

    /**
     * CodeIdTokenTokenResponseType constructor.
     *
     * @param AuthorizationCodeResponseType $codeResponseType
     * @param IdTokenResponseType           $idTokenResponseType
     * @param TokenResponseType             $tokenResponseType
     */
    public function __construct(AuthorizationCodeResponseType $codeResponseType, IdTokenResponseType $idTokenResponseType, TokenResponseType $tokenResponseType)
    {
        $this->codeResponseType = $codeResponseType;
        $this->idTokenResponseType = $idTokenResponseType;
        $this->tokenResponseType = $tokenResponseType;
    }

    /**
     * {@inheritdoc}
     */
    public function associatedGrantTypes(): array
    {
        return array_merge(
            $this->codeResponseType->associatedGrantTypes(),
            $this->idTokenResponseType->associatedGrantTypes(),
            $this->tokenResponseType->associatedGrantTypes()
        );
    }

    /**
     * {@inheritdoc}
     */
    public function name(): string
    {
        return 'code id_token token';
    }

    /**
     * {@inheritdoc}
     */
    public function getResponseMode(): string
    {
        return self::RESPONSE_TYPE_MODE_FRAGMENT;
    }

    /**
     * {@inheritdoc}
     */
    public function process(Authorization $authorization): Authorization
    {
        $authorization = $this->codeResponseType->process($authorization);
        $authorization = $this->idTokenResponseType->process($authorization);
        $authorization = $this->tokenResponseType->process($authorization);

        return $authorization;
    }
}
