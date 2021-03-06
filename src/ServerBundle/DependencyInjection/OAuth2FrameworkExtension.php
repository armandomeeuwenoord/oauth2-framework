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

namespace OAuth2Framework\ServerBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\Config\FileLocator;
use OAuth2Framework\ServerBundle\Component\Component;
use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

class OAuth2FrameworkExtension extends Extension implements PrependExtensionInterface
{
    /**
     * @var Component[]
     */
    private $components;

    /**
     * @var string
     */
    private $alias;

    /**
     * OAuth2FrameworkExtension constructor.
     *
     * @param string      $alias
     * @param Component[] $components
     */
    public function __construct(string $alias, array $components)
    {
        $this->alias = $alias;
        $this->components = $components;
    }

    /**
     * {@inheritdoc}
     */
    public function getAlias()
    {
        return $this->alias;
    }

    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $processor = new Processor();
        $config = $processor->processConfiguration($this->getConfiguration($configs, $container), $configs);

        foreach ($this->components as $component) {
            $component->load($config, $container);
        }

        $loader = new Loader\YamlFileLoader(
            $container,
            new FileLocator(__DIR__.'/../Resources/config')
        );
        $loader->load('config.yml');
        $loader->load('endpoint.yml');
        $loader->load('oauth2_response.yml');
        $loader->load('grant.yml');
        $loader->load('client_configuration.yml');
        $loader->load('client_registration.yml');
        $loader->load('services.yml');
        $loader->load('scope.yml');
        $loader->load('token.yml');
        $loader->load('openid_connect.yml');
        $loader->load('user_account_discovery.yml');
    }

    /**
     * @param array            $configs
     * @param ContainerBuilder $container
     *
     * @return Configuration
     */
    public function getConfiguration(array $configs, ContainerBuilder $container): Configuration
    {
        return new Configuration($this->getAlias(), $this->components);
    }

    /**
     * {@inheritdoc}
     */
    public function prepend(ContainerBuilder $container)
    {

        $configs = $container->getExtensionConfig($this->getAlias());
        $config = $this->processConfiguration($this->getConfiguration($configs, $container), $configs);

        foreach ($this->components as $component) {
            $result = $component->prepend($container, $config);
            if (!empty($result)) {
                $container->prependExtensionConfig($this->getAlias(), $result);
            }
        }
    }
}
