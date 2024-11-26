<?php

namespace Drenso\DeployerBundle\Generator;

use RuntimeException;
use Symfony\Component\Asset\Packages;
use Twig\Environment;

class UpdatePageGenerator
{
  private const BOOTSTRAP_CSS_FILENAME = 'bootstrap.min.css';

  /** @phpstan-ignore missingType.iterableValue */
  public function __construct(
    private readonly Packages $packages,
    private readonly Environment $twig,
    private array $configuration,
  ) {
    // Resolve SVG contents
    if ($svg = $configuration['svg']) {
      if (file_exists($svg) && is_readable($svg)) {
        $this->configuration['svg'] = file_get_contents($svg);
      }
    }

    // Resolve bootstrap checksum
    $cssFile = implode(DIRECTORY_SEPARATOR, [
      dirname(__DIR__),
      'Resources',
      'public',
      self::BOOTSTRAP_CSS_FILENAME,
    ]);
    if (!$cssContents = file_get_contents($cssFile)) {
      throw new RuntimeException('Failed to read bootstrap CSS file');
    }

    $hash        = hash('sha384', $cssContents, true);
    $hash_base64 = base64_encode($hash);

    $this->configuration['bootstrap_file']     = $this->packages->getUrl('bundles/drensodeployer/' . self::BOOTSTRAP_CSS_FILENAME);
    $this->configuration['bootstrap_checksum'] = "sha384-$hash_base64";
  }

  public function generateController(): string
  {
    return $this->twig->render('@DrensoDeployer/templates/UpdateControllerTemplate.php.twig', $this->configuration);
  }

  public function generateHtml(): string
  {
    return $this->twig->render('@DrensoDeployer/templates/UpdatePageTemplate.html.twig', $this->configuration);
  }
}
