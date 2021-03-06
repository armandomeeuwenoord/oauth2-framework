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

namespace OAuth2Framework\ServerBundle\Component\Core\Compiler;

use OAuth2Framework\Component\Core\Response\OAuth2ResponseFactoryManager;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class ResponseFactoryCompilerPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition('oauth2_server.oauth2_response_factory_manager')) {
            return;
        }

        $definition = $container->getDefinition('oauth2_server.oauth2_response_factory_manager');
        $taggedServices = $container->findTaggedServiceIds('oauth2_server_response_factory');
        foreach ($taggedServices as $id => $attributes) {
            $definition->addMethodCall('addResponseFactory', [$container->getDefinition($id)]);
        }
    }
}
