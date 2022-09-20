<?php

use Drenso\DeployerBundle\Command\GenerateScriptCommand;
use Drenso\DeployerBundle\Command\RunPostDeploymentTasksCommand;
use Drenso\DeployerBundle\Command\RunPreDeploymentTasksCommand;
use Drenso\DeployerBundle\DependencyInjection\DrensoDeployerExtension;
use Drenso\DeployerBundle\Executor\ScriptExecutor;
use Drenso\DeployerBundle\Executor\ScriptFinder;
use Drenso\DeployerBundle\Executor\ScriptLoader;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use function Symfony\Component\DependencyInjection\Loader\Configurator\param;
use function Symfony\Component\DependencyInjection\Loader\Configurator\service;

return function (ContainerConfigurator $configurator): void {
  $configurator->services()
      ->set(DrensoDeployerExtension::FINDER_ID, ScriptFinder::class)
      ->args([
          param(DrensoDeployerExtension::SCRIPTS_PATH_ID),
          param(DrensoDeployerExtension::NAMESPACE_ID),
      ])

      ->set(DrensoDeployerExtension::LOADER_ID, ScriptLoader::class)
      ->args([])

      ->set(DrensoDeployerExtension::EXECUTOR_ID, ScriptExecutor::class)
      ->args([
          service(DrensoDeployerExtension::FINDER_ID),
          service(DrensoDeployerExtension::LOADER_ID),
          service('doctrine.orm.entity_manager'),
      ])

      ->set(DrensoDeployerExtension::GEN_COMMAND_ID, GenerateScriptCommand::class)
      ->args([param(DrensoDeployerExtension::SCRIPTS_PATH_ID)])
      ->autoconfigure()

      ->set(DrensoDeployerExtension::PRE_COMMAND_ID, RunPreDeploymentTasksCommand::class)
      ->args([service(DrensoDeployerExtension::EXECUTOR_ID)])
      ->autoconfigure()

      ->set(DrensoDeployerExtension::POST_COMMAND_ID, RunPostDeploymentTasksCommand::class)
      ->args([service(DrensoDeployerExtension::EXECUTOR_ID)])
      ->autoconfigure();
};
