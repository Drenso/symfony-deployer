<?php

namespace Drenso\DeployerBundle\Generator;

use RuntimeException;
use Symfony\Component\Asset\Packages;
use Twig\Environment;

class UpdatePageGeneratorFactory
{
  public function __construct(private readonly ?Environment $twig, private readonly ?Packages $packages)
  {
  }

  /** @phpstan-ignore missingType.iterableValue */
  public function getGenerator(array $configuration): UpdatePageGenerator
  {
    if (!$this->twig) {
      throw new RuntimeException('Twig could not be loaded, is it installed?');
    }

    if (!$this->packages) {
      throw new RuntimeException('Symfony Asset component could not be loaded, is it installed?');
    }

    return new UpdatePageGenerator($this->packages, $this->twig, $configuration);
  }
}
