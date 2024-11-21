<?php

namespace Drenso\DeployerBundle\Controller;

use InvalidArgumentException;
use RuntimeException;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;

class UpdatePreviewController
{
  public function __construct(
    private readonly ?Environment $twig,
    private readonly array $configurations,
  ) {
  }

  public function preview(?string $name): Response
  {
    if (!$this->twig) {
      throw new RuntimeException('This route requires Twig te be installed.');
    }

    $configuration = $this->configurations[$name] ?? throw new InvalidArgumentException(
      sprintf('Update page configuration `%s` not found, did you configure it?', $name)
    );

    if ($svg = $configuration['svg']) {
      if (file_exists($svg) && is_readable($svg)) {
        $configuration['svg'] = file_get_contents($svg);
      }
    }

    return new Response($this->twig->render('@DrensoDeployer/templates/UpdatePageTemplate.html.twig', $configuration));
  }
}
