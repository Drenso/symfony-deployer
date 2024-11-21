<?php

namespace Drenso\DeployerBundle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Twig\Environment;

class GenerateUpdatePagesCommand extends Command
{
  public function __construct(
    private readonly ?Environment $twig,
    private readonly string $projectDir,
    private readonly array $configurations,
  ) {
    parent::__construct();
  }

  protected function configure(): void
  {
    $this
      ->setDescription('Generate the configured update pages')
      ->addArgument('controller-folder', mode: InputArgument::OPTIONAL, default: 'update/controllers')
      ->addArgument('content-folder', mode: InputArgument::OPTIONAL, default: 'update/content');
  }

  public function run(InputInterface $input, OutputInterface $output): int
  {
    $io = new SymfonyStyle($input, $output);

    if (!$this->twig) {
      $io->error('This command requires Twig te be installed.');

      return Command::FAILURE;
    }

    $controllerPath = $this->resolveFolder($input->getArgument('controller-folder'));
    $contentFolder  = $input->getArgument('content-folder');
    $contentPath    = $this->resolveFolder($contentFolder);

    foreach ($this->configurations as $name => $configuration) {
      $contentName                     = $name . '.html';
      $configuration['content_folder'] = $contentFolder . DIRECTORY_SEPARATOR . $contentName;

      if ($svg = $configuration['svg']) {
        if (file_exists($svg) && is_readable($svg)) {
          $configuration['svg'] = file_get_contents($svg);
        }
      }

      file_put_contents(
        $controllerPath . DIRECTORY_SEPARATOR . $name . '.php',
        $this->twig->render('@DrensoDeployer/templates/UpdateControllerTemplate.php.twig', $configuration)
      );
      file_put_contents(
        $contentPath . DIRECTORY_SEPARATOR . $contentName,
        $this->twig->render('@DrensoDeployer/templates/UpdatePageTemplate.html.twig', $configuration)
      );
    }

    $io->success('Generated new update pages');

    return Command::SUCCESS;
  }

  private function resolveFolder(string $folder): string
  {
    $folder = $this->projectDir . DIRECTORY_SEPARATOR . $folder;

    if (!file_exists($folder)) {
      mkdir($folder, 0755, true);
    }

    return $folder;
  }
}
