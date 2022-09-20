<?php

namespace Drenso\DeployerBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\PhpFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\ConfigurableExtension;

class DrensoDeployerExtension extends ConfigurableExtension
{
  public const BASE_ID         = 'drenso.deployer.';
  public const EXECUTOR_ID     = self::BASE_ID . 'executor';
  public const FINDER_ID       = self::BASE_ID . 'finder';
  public const LOADER_ID       = self::BASE_ID . 'loader';
  public const SCRIPTS_PATH_ID = self::BASE_ID . 'scripts.path';
  public const NAMESPACE_ID    = self::BASE_ID . 'scripts.namespace';
  public const GEN_COMMAND_ID  = self::BASE_ID . 'command.generate';
  public const PRE_COMMAND_ID  = self::BASE_ID . 'command.pre';
  public const POST_COMMAND_ID = self::BASE_ID . 'command.post';

  public function loadInternal(array $mergedConfig, ContainerBuilder $container): void
  {
    // Load configured services
    $loader = new PhpFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
    $loader->load('services.php');

    // Register parameter
    $container->setParameter(self::SCRIPTS_PATH_ID, $mergedConfig['path']);
    $container->setParameter(self::NAMESPACE_ID, $mergedConfig['namespace']);
  }
}
