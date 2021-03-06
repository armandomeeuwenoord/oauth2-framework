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

namespace OAuth2Framework\ServerBundle\Component\OpenIdConnect;

use OAuth2Framework\ServerBundle\Component\Component;
use Jose\Bundle\JoseFramework\Helper\ConfigurationHelper;
use OAuth2Framework\ServerBundle\Component\OpenIdConnect\Compiler\ClaimSourceCompilerPass;
use OAuth2Framework\ServerBundle\Component\OpenIdConnect\Compiler\IdTokenMetadataCompilerPass;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class IdTokenSource implements Component
{
    /**
     * {@inheritdoc}
     */
    public function name(): string
    {
        return 'id_token';
    }

    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $config = $configs['openid_connect']['id_token'];
        $container->setParameter('oauth2_server.openid_connect.id_token.enabled', $config['enabled']);
        if (!$config['enabled']) {
            return;
        }

        $container->setParameter('oauth2_server.openid_connect.id_token.default_signature_algorithm', $config['default_signature_algorithm']);
        $container->setParameter('oauth2_server.openid_connect.id_token.signature_algorithmsy', $config['signature_algorithms']);
        $container->setParameter('oauth2_server.openid_connect.id_token.lifetime', $config['lifetime']);
        $container->setParameter('oauth2_server.openid_connect.id_token.encryption.enabled', $config['encryption']['enabled']);
        $container->setParameter('oauth2_server.openid_connect.id_token.encryption.key_encryption_algorithms', $config['encryption']['key_encryption_algorithms']);
        $container->setParameter('oauth2_server.openid_connect.id_token.encryption.content_encryption_algorithms', $config['encryption']['content_encryption_algorithms']);
    }

    /**
     * {@inheritdoc}
     */
    public function getNodeDefinition(ArrayNodeDefinition $node, ArrayNodeDefinition $rootNode)
    {
        $node->children()
            ->arrayNode($this->name())
                ->canBeEnabled()
                ->addDefaultsIfNotSet()
                ->validate()
                    ->ifTrue(function ($config) {
                        return empty($config['default_signature_algorithm']);
                    })
                    ->thenInvalid('The option "default_signature_algorithm" must be set.')
                ->end()
                ->validate()
                    ->ifTrue(function ($config) {
                        return empty($config['signature_algorithms']);
                    })
                    ->thenInvalid('The option "signature_algorithm" must contain at least one signature algorithm.')
                ->end()
                ->validate()
                    ->ifTrue(function ($config) {
                        return !in_array($config['default_signature_algorithm'], $config['signature_algorithms']);
                    })
                    ->thenInvalid('The default signature algorithm must be in the supported signature algorithms.')
                ->end()
                ->children()
                    ->scalarNode('default_signature_algorithm')
                    ->info('Signature algorithm used if the client has not defined a preferred one. Recommended value is "RS256".')
                ->end()
                ->arrayNode('signature_algorithms')
                    ->info('Signature algorithm used to sign the ID Tokens.')
                    ->useAttributeAsKey('name')
                    ->scalarPrototype()->end()
                    ->treatNullLike([])
                    ->treatFalseLike([])
                ->end()
                ->arrayNode('claim_checkers')
                    ->info('Checkers will verify the JWT claims.')
                    ->useAttributeAsKey('name')
                    ->scalarPrototype()->end()
                    ->treatNullLike(['exp', 'iat', 'nbf'])
                ->end()
                ->arrayNode('header_checkers')
                    ->info('Checkers will verify the JWT headers.')
                    ->useAttributeAsKey('name')
                    ->scalarPrototype()->end()
                    ->treatNullLike([])
                    ->treatFalseLike([])
                ->end()
                ->integerNode('lifetime')
                    ->info('Lifetime of the ID Tokens (in seconds). If an access token is issued with the ID Token, the lifetime of the access token is used instead of this value.')
                    ->defaultValue(3600)
                    ->min(1)
                ->end()
                ->arrayNode('encryption')
                    ->canBeEnabled()
                    ->children()
                        ->arrayNode('key_encryption_algorithms')
                            ->info('Supported key encryption algorithms.')
                            ->useAttributeAsKey('name')
                            ->scalarPrototype()->end()
                            ->treatNullLike([])
                            ->treatFalseLike([])
                        ->end()
                        ->arrayNode('content_encryption_algorithms')
                            ->info('Supported content encryption algorithms.')
                            ->useAttributeAsKey('name')
                            ->scalarPrototype()->end()
                            ->treatNullLike([])
                            ->treatFalseLike([])
                        ->end()
                    ->end()
                ->end()
            ->end()
        ->end();
    }

    /**
     * {@inheritdoc}
     */
    public function build(ContainerBuilder $container)
    {
        $container->addCompilerPass(new ClaimSourceCompilerPass());
        $container->addCompilerPass(new IdTokenMetadataCompilerPass());
    }

    /**
     * {@inheritdoc}
     */
    public function prepend(ContainerBuilder $container, array $bundleConfig): array
    {
        $config = $bundleConfig['openid_connect']['id_token'];
        if (!$config['enabled']) {
            return [];
        }

        ConfigurationHelper::addJWSBuilder($container, $this->name(), $config['signature_algorithms'], true);
        ConfigurationHelper::addJWSLoader($container, $this->name(), ['jws_compact'], $config['signature_algorithms'], [], true);

//        Assertion::keyExists($bundleConfig['key_set'], 'signature', 'The signature key set must be enabled.');
        ConfigurationHelper::addKeyset($container, 'id_token.key_set.signature', 'jwkset', ['value' => $bundleConfig['key_set']['signature']]);

        return [];
    }
}
