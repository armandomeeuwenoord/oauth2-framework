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
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\PhpFileLoader;
use OAuth2Framework\ServerBundle\Component\OpenIdConnect\Compiler\UserinfoRouteCompilerPass;
use OAuth2Framework\ServerBundle\Component\OpenIdConnect\Compiler\UserInfoScopeSupportCompilerPass;

class UserinfoEndpointSource implements Component
{
    /**
     * @var Component[]
     */
    private $subComponents;

    public function __construct()
    {
        $this->subComponents = [
            new UserinfoEndpointSignatureSource(),
            new UserinfoEndpointEncryptionSource(),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function name(): string
    {
        return 'userinfo_endpoint';
    }

    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
//        if (!$configs['openid_connect']['userinfo_endpoint']['enabled']) {
//            return;
//        }

        $container->setParameter('oauth2_server.openid_connect.userinfo_endpoint.path', $configs['openid_connect']['userinfo_endpoint']['path']);

        foreach ($this->subComponents as $subComponent) {
            $subComponent->load($configs, $container);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getNodeDefinition(ArrayNodeDefinition $node, ArrayNodeDefinition $rootNode)
    {
        $childNode = $node->children()
            ->arrayNode($this->name())
                ->canBeEnabled();

        $childNode->children()
            ->scalarNode('path')
                ->defaultValue('/userinfo')
            ->end()
        ->end();

        foreach ($this->subComponents as $subComponent) {
            $subComponent->getNodeDefinition($childNode, $node);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function prepend(ContainerBuilder $container, array $config): array
    {
        $updatedConfig = [];
        foreach ($this->subComponents as $subComponent) {
            $updatedConfig = array_merge(
                $updatedConfig,
                $subComponent->prepend($container, $config)
            );
        }

        return $updatedConfig;
    }

    /**
     * {@inheritdoc}
     */
    public function build(ContainerBuilder $container)
    {

        $container->addCompilerPass(new UserinfoRouteCompilerPass());
        $container->addCompilerPass(new UserInfoScopeSupportCompilerPass());

        foreach ($this->subComponents as $component) {
            $component->build($container);
        }
    }
}
