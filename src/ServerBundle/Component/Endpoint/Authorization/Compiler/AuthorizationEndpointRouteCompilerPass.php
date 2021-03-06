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

use OAuth2Framework\ServerBundle\Routing\RouteLoader;
use OAuth2Framework\ServerBundle\Service\MetadataBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class AuthorizationEndpointRouteCompilerPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->has('oauth2_server.endpoint.authorization_endpoint_pipe')) {
            return;
        }

        $path = $container->getParameter('oauth2_server.endpoint.authorization.path');
        $host = $container->getParameter('oauth2_server.endpoint.authorization.host');
        $route_loader = $container->getDefinition('oauth2_server.services_route_loader');
        $route_loader->addMethodCall('addRoute', [
            'authorization_endpoint',
            'oauth2_server.endpoint.authorization_endpoint_pipe',
            'dispatch',
            $path, // path
            [], // defaults
            [], // requirements
            [], // options
            $host, // host
            ['https'], // schemes
            ['GET', 'POST'], // methods
            '', // condition
        ]);

        if (!$container->hasDefinition('oauth2_server.metadata_builder')) {
            return;
        }
        $definition = $container->getDefinition('oauth2_server.metadata_builder');
        $definition->addMethodCall('addRoute', ['authorization_endpoint', 'oauth2_server_authorization_endpoint']);
    }
}
