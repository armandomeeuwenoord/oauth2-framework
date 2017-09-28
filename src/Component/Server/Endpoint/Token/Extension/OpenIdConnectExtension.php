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

namespace OAuth2Framework\Component\Server\Endpoint\Token\Extension;

use Jose\Component\Core\JWKSet;
use Jose\Component\Encryption\JWEBuilder;
use Jose\Component\Signature\JWSBuilder;
use OAuth2Framework\Component\Server\Model\AccessToken\AccessToken;
use OAuth2Framework\Component\Server\Model\Client\Client;
use OAuth2Framework\Component\Server\Model\IdToken\IdTokenBuilderFactory;
use OAuth2Framework\Component\Server\Model\ResourceOwner\ResourceOwnerInterface;
use OAuth2Framework\Component\Server\Model\UserAccount\UserAccountInterface;

final class OpenIdConnectExtension implements TokenEndpointExtensionInterface
{
    /**
     * @var JWKSet
     */
    private $signatureKeys;

    /**
     * @var JWSBuilder
     */
    private $jwsBuilder;

    /**
     * @var JWEBuilder|null
     */
    private $jweBuilder;

    /**
     * @var IdTokenBuilderFactory
     */
    private $idTokenBuilderFactory;

    /**
     * @var string
     */
    private $defaultSignatureAlgorithm;

    /**
     * OpenIdConnectExtension constructor.
     *
     * @param IdTokenBuilderFactory   $idTokenBuilderFactory
     * @param string                  $defaultSignatureAlgorithm
     * @param JWSBuilder         $jwsBuilder
     * @param JWKSet         $signatureKeys
     * @param JWEBuilder|null $jweBuilder
     */
    public function __construct(IdTokenBuilderFactory $idTokenBuilderFactory, string $defaultSignatureAlgorithm, JWSBuilder $jwsBuilder, JWKSet $signatureKeys, ?JWEBuilder $jweBuilder)
    {
        $this->idTokenBuilderFactory = $idTokenBuilderFactory;
        $this->defaultSignatureAlgorithm = $defaultSignatureAlgorithm;
        $this->jwsBuilder = $jwsBuilder;
        $this->signatureKeys = $signatureKeys;
        $this->jweBuilder = $jweBuilder;
    }

    public function process(Client $client, ResourceOwnerInterface $resourceOwner, AccessToken $accessToken, callable $next): array
    {
        if ($resourceOwner instanceof UserAccountInterface && true === $accessToken->hasScope('openid') && $accessToken->hasMetadata('redirect_uri')) {
            $idToken = $this->issueIdToken($client, $resourceOwner, $accessToken);
            $data = $next($client, $resourceOwner, $accessToken);
            $data['id_token'] = $idToken;

            return $data;
        }

        return $next($client, $resourceOwner, $accessToken);
    }

    /**
     * @param Client               $client
     * @param UserAccountInterface $userAccount
     * @param AccessToken          $accessToken
     *
     * @return string
     */
    private function issueIdToken(Client $client, UserAccountInterface $userAccount, AccessToken $accessToken): string
    {
        $redirectUri = $accessToken->getMetadata('redirect_uri');
        $idTokenBuilder = $this->idTokenBuilderFactory->createBuilder($client, $userAccount, $redirectUri);

        $requestedClaims = $this->getIdTokenClaims($accessToken);
        $idTokenBuilder = $idTokenBuilder->withRequestedClaims($requestedClaims);

        $idTokenBuilder = $idTokenBuilder->withAccessTokenId($accessToken->getTokenId());

        if ($client->has('id_token_signed_response_alg')) {
            $signatureAlgorithm = $client->get('id_token_signed_response_alg');
            $idTokenBuilder = $idTokenBuilder->withSignature($this->jwsBuilder, $this->signatureKeys, $signatureAlgorithm);
        } else {
            $idTokenBuilder = $idTokenBuilder->withSignature($this->jwsBuilder, $this->signatureKeys, $this->defaultSignatureAlgorithm);
        }
        if ($client->has('id_token_encrypted_response_alg') && $client->has('id_token_encrypted_response_enc') && null !== $this->jweBuilder) {
            $keyEncryptionAlgorithm = $client->get('id_token_encrypted_response_alg');
            $contentEncryptionAlgorithm = $client->get('id_token_encrypted_response_enc');
            $idTokenBuilder = $idTokenBuilder->withEncryption($this->jweBuilder, $keyEncryptionAlgorithm, $contentEncryptionAlgorithm);
        }
        $idTokenBuilder = $idTokenBuilder->withAccessToken($accessToken);

        return $idTokenBuilder->build();
    }

    /**
     * @param AccessToken $accessToken
     *
     * @return array
     */
    private function getIdTokenClaims(AccessToken $accessToken): array
    {
        if (!$accessToken->hasMetadata('requested_claims')) {
            return [];
        }

        $requestedClaims = $accessToken->getMetadata('requested_claims');
        if (true === array_key_exists('id_token', $requestedClaims)) {
            return $requestedClaims['id_token'];
        }

        return [];
    }
}
