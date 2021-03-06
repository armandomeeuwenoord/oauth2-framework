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

namespace OAuth2Framework\ServerBundle\DependencyInjection\Compiler;

use OAuth2Framework\ServerBundle\Routing\RouteLoader;
use OAuth2Framework\ServerBundle\Service\MetadataBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class SessionManagementRouteCompilerPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition('oauth2_server.session_management_pipe') || !$container->getParameter('oauth2_server.endpoint.session_management.enabled')) {
            return;
        }

        $path = $container->getParameter('oauth2_server.endpoint.session_management.path');
        $host = $container->getParameter('oauth2_server.endpoint.session_management.host');
        $route_loader = $container->getDefinition('oauth2_server.services_route_loader');
        $route_loader->addMethodCall('addRoute', [
            'openid_connect_iframe_endpoint',
            'oauth2_server.session_management_pipe',
            'dispatch',
            $path, // path
            [], // defaults
            [], // requirements
            [], // options
            $host, // host
            ['https'], // schemes
            ['GET'], // methods
            '', // condition
        ]);

        if (!$container->hasDefinition('oauth2_server.metadata_builder')) {
            return;
        }
        $medata = $container->getDefinition('oauth2_server.metadata_builder');
        $medata->addMethodCall('addRoute', ['check_session_iframe', 'oauth2_server_openid_connect_iframe_endpoint']);
    }
}
