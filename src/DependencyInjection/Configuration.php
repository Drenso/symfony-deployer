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

    $rootNode->children()
        ->scalarNode('generator_enabled')
        ->info('Whether the generator command is available')
        ->defaultTrue();

    $rootNode->children()
      ->arrayNode('update_pages')
        ->info('A generator for static update pages')
        ->canBeEnabled()
        ->addDefaultsIfNotSet()
        ->children()
          ->booleanNode('preview_controller')
            ->info('Whether the preview controller needs to be registered. Routes still need to be imported manually.')
            ->defaultFalse()
          ->end()
          ->arrayNode('configurations')
            ->info('Update page configurations')
            ->useAttributeAsKey('name')
            ->arrayPrototype()
            ->children()
              ->integerNode('response_code')->info('The response code to use with this page')->defaultValue(503)->end()
              ->scalarNode('page_title')->defaultValue('Updating')->end()
              ->scalarNode('background_color')->defaultValue('#244E95')->end()
              ->scalarNode('svg')->defaultNull()->end()
              ->scalarNode('update_title')->defaultValue('Updating')->end()
              ->scalarNode('update_text')
                ->defaultValue('Unfortunately, we are updating the website right now. However, this shouldn\'t take very long, so please try again in a few minutes!')
              ->end()
              ->booleanNode('enable_game')->info('Whether to enable the Chromium dino mini game on the update page')->defaultTrue()->end()
              ->scalarNode('game_header')->defaultValue('Need to pass some time?')->end();

    return $treeBuilder;
  }
}
