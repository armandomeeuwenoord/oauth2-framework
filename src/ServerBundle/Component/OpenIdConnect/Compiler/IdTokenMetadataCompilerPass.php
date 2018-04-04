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

namespace OAuth2Framework\ServerBundle\Component\OpenIdConnect\Compiler;

use OAuth2Framework\ServerBundle\Service\MetadataBuilder;
use OAuth2Framework\Component\OpenIdConnect\UserInfo\UserInfo;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class IdTokenMetadataCompilerPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition('oauth2_server.metadata_builder') || !$container->hasDefinition('oauth2_server.openid_connect.user_info')) {
            return;
        }
        $metadata = $container->getDefinition('oauth2_server.metadata_builder');

        $metadata->addMethodCall('setUserinfo', [new Reference('oauth2_server.openid_connect.user_info')]);
        $metadata->addMethodCall('addKeyValuePair', ['claim_types_supported', ['normal', 'aggregated', 'distributed']]);
        $metadata->addMethodCall('addKeyValuePair', ['claims_parameter_supported', true]);
        $metadata->addMethodCall('addKeyValuePair', ['id_token_signing_alg_values_supported', $container->getParameter('oauth2_server.openid_connect.id_token.default_signature_algorithm')]);

        var_dump($container->getParameter('oauth2_server.openid_connect.id_token.encryption.enabled'));

        if (true === $container->getParameter('oauth2_server.openid_connect.id_token.encryption.enabled')) {
            $metadata->addMethodCall('addKeyValuePair', ['id_token_encryption_alg_values_supported', $container->getParameter('oauth2_server.openid_connect.id_token.encryption.key_encryption_algorithms')]);
            $metadata->addMethodCall('addKeyValuePair', ['id_token_encryption_enc_values_supported', $container->getParameter('oauth2_server.openid_connect.id_token.encryption.content_encryption_algorithms')]);
        }
    }
}
