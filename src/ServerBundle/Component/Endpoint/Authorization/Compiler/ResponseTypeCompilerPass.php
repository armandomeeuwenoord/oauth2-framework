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

namespace OAuth2Framework\ServerBundle\Component\Endpoint\Authorization\Compiler;

use OAuth2Framework\ServerBundle\Service\MetadataBuilder;
use OAuth2Framework\Component\AuthorizationEndpoint\ResponseTypeManager;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class ResponseTypeCompilerPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition('oauth2_server.endpoint.authorization_response_type_manager')) {
            return;
        }

        $definition = $container->getDefinition('oauth2_server.endpoint.authorization_response_type_manager');

        $taggedServices = $container->findTaggedServiceIds('oauth2_server_response_type');
        foreach ($taggedServices as $id => $attributes) {
            $definition->addMethodCall('add', [new Reference($id)]);
        }

        if ($container->hasDefinition('oauth2_server.metadata_builder')) {
            $metadata = $container->getDefinition('oauth2_server.metadata_builder');
            $metadata->addMethodCall('setResponseTypeManager', [new Reference('oauth2_server.endpoint.authorization_response_type_manager')]);
        }
    }
}
