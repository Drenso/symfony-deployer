<?php

namespace Drenso\DeployerBundle;

use Doctrine\Bundle\DoctrineBundle\DependencyInjection\Compiler\DoctrineOrmMappingsPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class DrensoDeployerBundle extends Bundle
{
  public function build(ContainerBuilder $container)
  {
    parent::build($container);

    // Register entity with the container
    $container->addCompilerPass(DoctrineOrmMappingsPass::createAttributeMappingDriver(
        ['DrensoDeployerBundle\Entity'],
        [realpath(__DIR__ . '/Entity')],
        [],
        false,
        ['DrensoDeployerBundle' => 'DrensoDeployerBundle\Entity']
    ));
  }
}
