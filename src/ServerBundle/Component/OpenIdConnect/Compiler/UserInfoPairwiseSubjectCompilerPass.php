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

use OAuth2Framework\Component\AuthorizationEndpoint\UserAccountDiscovery\IdTokenHintDiscovery;
use OAuth2Framework\Component\OpenIdConnect\UserInfo\UserInfo;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class UserInfoPairwiseSubjectCompilerPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasAlias('oauth2_server.openid_connect.pairwise_subject.service')) {
            return;
        }

        $definition = $container->getDefinition('oauth2_server.openid_connect.user_info');
        $service = $container->getAlias('oauth2_server.openid_connect.pairwise_subject.service');
        $isDefault = $container->getParameter('oauth2_server.openid_connect.pairwise_subject.is_default');
        $definition->addMethodCall('enablePairwiseSubject', [new Reference($service), $isDefault]);

        // Enabled the pairwise support for the Id Token Hint Discovery service if available
        if ($container->hasDefinition('oauth2_server.openid_connect.id_token_hint_discovery')) {
            $definition = $container->getDefinition('oauth2_server.openid_connect.id_token_hint_discovery');
            $definition->addMethodCall('enablePairwiseSubject', [new Reference($service)]);
        }
    }
}
