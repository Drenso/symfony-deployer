<?php

namespace Drenso\DeployerBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
  public function getConfigTreeBuilder(): TreeBuilder
  {
    $treeBuilder = new TreeBuilder('drenso_deployer');

    $rootNode = $treeBuilder->getRootNode();
    $rootNode->children()
        ->scalarNode('path')
        ->info('The path where the deployment scripts are to be found')
        ->defaultValue('%kernel.project_dir%' . DIRECTORY_SEPARATOR . 'deploy' . DIRECTORY_SEPARATOR . 'scripts');

    $rootNode->children()
        ->scalarNode('namespace')
        ->info('The namespace of the deployment scripts')
        ->defaultValue('DrensoDeployer');

    return $treeBuilder;
  }
}
