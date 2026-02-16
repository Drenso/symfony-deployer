<?php

namespace Drenso\DeployerBundle\Controller;

use Drenso\DeployerBundle\Generator\UpdatePageGeneratorFactory;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

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
    if (!$name) {
      throw new NotFoundHttpException('Missing page configuration name');
    }

    $configuration = $this->configurations[$name] ?? throw new NotFoundHttpException(
      sprintf('Update page configuration `%s` not found, did you configure it?', $name)
    );

    return new Response($this->generatorFactory->getGenerator($configuration)->generateHtml());
  }
}
