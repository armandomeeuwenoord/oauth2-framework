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

use OAuth2Framework\ServerBundle\Security\Authentication\Provider\OAuth2Provider;
use OAuth2Framework\ServerBundle\Security\Firewall\OAuth2Listener;
use OAuth2Framework\ServerBundle\Security\EntryPoint\OAuth2EntryPoint;
use OAuth2Framework\ServerBundle\Annotation\AnnotationDriver;
use OAuth2Framework\ServerBundle\Annotation\Checker;
use OAuth2Framework\Component\Core\Response\OAuth2ResponseFactoryManager;
use OAuth2Framework\Component\TokenType\TokenTypeManager;

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use function Symfony\Component\DependencyInjection\Loader\Configurator\ref;

return function (ContainerConfigurator $container) {
    $container = $container->services()->defaults()
        ->private()
        ->autoconfigure()
        ->autowire();

    $container->set(OAuth2Provider::class)
        ->private();

    $container->set(OAuth2Listener::class)
        ->private();

    $container->set(OAuth2EntryPoint::class)
        ->private();

    $container->set(AnnotationDriver::class)
        ->args([
            ref('annotations.cached_reader'),
            ref('security.token_storage'),
            ref(TokenTypeManager::class),
            ref(OAuth2ResponseFactoryManager::class),
        ])
        ->tag('kernel.event_listener', ['event' => 'kernel.controller', 'method' => 'onKernelController']);

    $container->set(Checker\ClientIdChecker::class)
        ->tag('oauth2_server.security.annotation_checker')
        ->private();

    $container->set(Checker\ResourceOwnerIdChecker::class)
        ->tag('oauth2_server.security.annotation_checker')
        ->private();

    $container->set(Checker\ScopeChecker::class)
        ->tag('oauth2_server.security.annotation_checker')
        ->private();
};