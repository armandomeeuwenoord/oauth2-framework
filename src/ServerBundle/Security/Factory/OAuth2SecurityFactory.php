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

namespace OAuth2Framework\ServerBundle\Security\Factory;

use OAuth2Framework\ServerBundle\Security\Authentication\Provider\OAuth2Provider;
use OAuth2Framework\ServerBundle\Security\EntryPoint\OAuth2EntryPoint;
use OAuth2Framework\ServerBundle\Security\Firewall\OAuth2Listener;
use Symfony\Component\DependencyInjection\ChildDefinition;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\Config\Definition\Builder\NodeDefinition;
use Symfony\Bundle\SecurityBundle\DependencyInjection\Security\Factory\SecurityFactoryInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class OAuth2SecurityFactory implements SecurityFactoryInterface
{
    public function create(ContainerBuilder $container, $id, $config, $userProvider, $defaultEntryPoint)
    {

        $definitionDecorator = 'Symfony\\Component\\DependencyInjection\\DefinitionDecorator';
        $childDefinition = 'Symfony\\Component\\DependencyInjection\\ChildDefinition';
        $definitionClass = $childDefinition;
        if (class_exists($definitionDecorator)) {
            $definitionClass = $definitionDecorator;
        }

        $providerId = 'security.authentication.provider.oauth2.'.$id;
        $container
            ->setDefinition($providerId, new $definitionClass('oauth2_server.security_oauth2_provider'))
//            ->replaceArgument(0, new Reference($userProvider))
        ;

        $listenerId = 'security.authentication.listener.oauth2.'.$id;
        $container->setDefinition($listenerId, new $definitionClass('oauth2_server.security_oauth2_listener'));

        return array($providerId, $listenerId, 'oauth2_server.security_oauth2_entrypoint');
    }

    /**
     * {@inheritdoc}
     */
    public function getPosition()
    {
        return 'pre_auth';
    }

    /**
     * {@inheritdoc}
     */
    public function getKey()
    {
        return 'oauth2';
    }

    /**
     * {@inheritdoc}
     */
    public function addConfiguration(NodeDefinition $node)
    {
        $node
            ->children()
                ->scalarNode('access_token_handler_manager')
                    ->info('The access token handler manager has to retrieve access tokens on demand. Access token can be find from a database, the introspection or any other method.')
                    ->isRequired()
                ->end()
            ->end();
    }
}
