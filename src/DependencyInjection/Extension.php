<?php

declare(strict_types=1);

namespace Factotum\SerializedStorageStructuredTableBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension as SymfonyExtension;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\Config\FileLocator;

class Extension extends SymfonyExtension
{
    private const BUNDLE_ALIAS = 'factotum_serialized_storage_structured_table_bundle';
    private const CONFIG_DIR = '/../../config';
    private const SERVICES_FILE = 'services.yaml';

    /**
     * @param array $configs
     * @param ContainerBuilder $container
     * @return void
     * @throws \Exception
     */
    public function load(array $configs, ContainerBuilder $container): void
    {
        $loader = new YamlFileLoader(
            $container,
            new FileLocator(__DIR__ . self::CONFIG_DIR)
        );

        $loader->load(self::SERVICES_FILE);
    }

    /**
     * @return string
     */
    public function getAlias(): string
    {
        return self::BUNDLE_ALIAS;
    }
}
