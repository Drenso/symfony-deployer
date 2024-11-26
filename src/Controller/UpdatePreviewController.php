<?php

namespace Drenso\DeployerBundle\Controller;

use Drenso\DeployerBundle\Generator\UpdatePageGeneratorFactory;
use InvalidArgumentException;
use Symfony\Component\HttpFoundation\Response;

class UpdatePreviewController
{
  /** @phpstan-ignore missingType.iterableValue */
  public function __construct(
    private readonly UpdatePageGeneratorFactory $generatorFactory,
    private readonly array $configurations,
  ) {
  }

  public function preview(?string $name): Response
  {
    $configuration = $this->configurations[$name] ?? throw new InvalidArgumentException(
      sprintf('Update page configuration `%s` not found, did you configure it?', $name)
    );

    return new Response($this->generatorFactory->getGenerator($configuration)->generateHtml());
  }
}
