<?php

namespace Drenso\DeployerBundle\DependencyInjection;

use Drenso\DeployerBundle\Controller\UpdatePreviewController;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\PhpFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\ConfigurableExtension;

class DrensoDeployerExtension extends ConfigurableExtension
{
  private const BASE_ID                        = 'drenso.deployer.';
  final public const SERVICE_EXECUTOR_ID       = self::BASE_ID . 'service.executor';
  final public const SERVICE_FINDER_ID         = self::BASE_ID . 'service.finder';
  final public const SERVICE_LOADER_ID         = self::BASE_ID . 'service.loader';
  final public const PARAM_SCRIPTS_PATH_ID     = self::BASE_ID . 'param.scripts_path';
  final public const PARAM_NAMESPACE_ID        = self::BASE_ID . 'param.namespace';
  final public const PARAM_UPDATE_PAGES        = self::BASE_ID . 'param.update_pages';
  final public const COMMAND_GENERATE_ID       = self::BASE_ID . 'command.generate';
  final public const COMMAND_PRE_ID            = self::BASE_ID . 'command.pre';
  final public const COMMAND_POST_ID           = self::BASE_ID . 'command.post';
  final public const COMMAND_GENERATE_PAGES_ID = self::BASE_ID . 'command.generate_pages';
  final public const TAG_DEPENDENCY            = self::BASE_ID . 'executer.dependency';

  public function loadInternal(array $mergedConfig, ContainerBuilder $container): void
  {
    // Load configured services
    $loader = new PhpFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
    $loader->load('services.php');

    // Register parameters
    $container->setParameter(self::PARAM_SCRIPTS_PATH_ID, $mergedConfig['path']);
    $container->setParameter(self::PARAM_NAMESPACE_ID, $mergedConfig['namespace']);

    $updatePagesConfig = $mergedConfig['update_pages'];
    if ($updatePagesConfig['enabled']) {
      $container->setParameter(self::PARAM_UPDATE_PAGES, $updatePagesConfig['configurations']);

      if (!$updatePagesConfig['preview_controller']) {
        $container->removeDefinition(UpdatePreviewController::class);
      }
    } else {
      $container->removeDefinition(self::COMMAND_GENERATE_PAGES_ID);
      $container->removeDefinition(UpdatePreviewController::class);
    }
  }
}
