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

namespace OAuth2Framework\Bundle\Server\DependencyInjection\Compiler;

use OAuth2Framework\Bundle\Server\Routing\RouteLoader;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class TokenRevocationRouteCompilerPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition('token_revocation_pipe')) {
            return;
        }

        $path = $container->getParameter('oauth2_server.endpoint.token_revocation.path');
        $route_loader = $container->getDefinition(RouteLoader::class);
        $route_loader->addMethodCall('addRoute', [
            'token_revocation_endpoint',
            'token_revocation_pipe',
            'dispatch',
            $path, // path
            [], // defaults
            [], // requirements
            [], // options
            '', // host
            ['https'], // schemes
            ['GET', 'POST'], // methods
            '', // condition
        ]);

        /*if (!$container->hasDefinition('oauth2_server.openid_connect.metadata')) {
            return;
        }

        $definition = $container->getDefinition('oauth2_server.openid_connect.metadata');
        $definition->addMethodCall('setRoute', ['token_revocation_endpoint', 'oauth2_server_token_revocation_endpoint']);*/
    }
}