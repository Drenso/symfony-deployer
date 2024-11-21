<?php

use Drenso\DeployerBundle\Command\GenerateScriptCommand;
use Drenso\DeployerBundle\Command\GenerateUpdatePagesCommand;
use Drenso\DeployerBundle\Command\RunPostDeploymentTasksCommand;
use Drenso\DeployerBundle\Command\RunPreDeploymentTasksCommand;
use Drenso\DeployerBundle\Controller\UpdatePreviewController;
use Drenso\DeployerBundle\DependencyInjection\DrensoDeployerExtension;
use Drenso\DeployerBundle\Executor\ScriptExecutor;
use Drenso\DeployerBundle\Executor\ScriptFinder;
use Drenso\DeployerBundle\Executor\ScriptLoader;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

use function Symfony\Component\DependencyInjection\Loader\Configurator\param;
use function Symfony\Component\DependencyInjection\Loader\Configurator\service;
use function Symfony\Component\DependencyInjection\Loader\Configurator\tagged_locator;

return function (ContainerConfigurator $configurator): void {
  $configurator->services()
    ->set(DrensoDeployerExtension::SERVICE_FINDER_ID, ScriptFinder::class)
    ->args([param(DrensoDeployerExtension::PARAM_SCRIPTS_PATH_ID)])

    ->set(DrensoDeployerExtension::SERVICE_LOADER_ID, ScriptLoader::class)
    ->args([param(DrensoDeployerExtension::PARAM_NAMESPACE_ID)])

    ->set(DrensoDeployerExtension::SERVICE_EXECUTOR_ID, ScriptExecutor::class)
    ->args([
      service(DrensoDeployerExtension::SERVICE_FINDER_ID),
      service(DrensoDeployerExtension::SERVICE_LOADER_ID),
      tagged_locator(DrensoDeployerExtension::TAG_DEPENDENCY),
      service('parameter_bag'),
      service('doctrine.orm.entity_manager'),
      service('messenger.default_bus')->nullOnInvalid(),
    ])

    ->set(DrensoDeployerExtension::COMMAND_GENERATE_ID, GenerateScriptCommand::class)
    ->args([
      param(DrensoDeployerExtension::PARAM_SCRIPTS_PATH_ID),
      param(DrensoDeployerExtension::PARAM_NAMESPACE_ID),
      service('twig')->nullOnInvalid(),
    ])
    ->tag('console.command', ['command' => 'drenso:deployer:generate'])

    ->set(DrensoDeployerExtension::COMMAND_PRE_ID, RunPreDeploymentTasksCommand::class)
    ->args([service(DrensoDeployerExtension::SERVICE_EXECUTOR_ID)])
    ->tag('console.command', ['command' => 'drenso:deployer:pre'])

    ->set(DrensoDeployerExtension::COMMAND_POST_ID, RunPostDeploymentTasksCommand::class)
    ->args([service(DrensoDeployerExtension::SERVICE_EXECUTOR_ID)])
    ->tag('console.command', ['command' => 'drenso:deployer:post'])

    ->set(UpdatePreviewController::class)
    ->args([
      service('twig')->nullOnInvalid(),
      param(DrensoDeployerExtension::PARAM_UPDATE_PAGES),
    ])
    ->tag('controller.service_arguments')

    ->set(DrensoDeployerExtension::COMMAND_GENERATE_PAGES_ID, GenerateUpdatePagesCommand::class)
    ->args([
      service('twig')->nullOnInvalid(),
      param('kernel.project_dir'),
      param(DrensoDeployerExtension::PARAM_UPDATE_PAGES),
    ])
    ->tag('console.command', ['command' => 'drenso:deployer:generate-update-pages']);
};
