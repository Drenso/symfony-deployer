<?php

namespace Drenso\DeployerBundle\Command;

use Drenso\DeployerBundle\Generator\UpdatePageGeneratorFactory;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class GenerateUpdatePagesCommand extends Command
{
  /** @phpstan-ignore missingType.iterableValue */
  public function __construct(
    private readonly UpdatePageGeneratorFactory $generatorFactory,
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

    $controllerPath = $this->resolveFolder($input->getArgument('controller-folder'));
    $contentFolder  = $input->getArgument('content-folder');
    $contentPath    = $this->resolveFolder($contentFolder);

    foreach ($this->configurations as $name => $configuration) {
      $contentName                     = $name . '.html';
      $configuration['content_folder'] = $contentFolder . DIRECTORY_SEPARATOR . $contentName;

      $generator = $this->generatorFactory->getGenerator($configuration);

      file_put_contents($controllerPath . DIRECTORY_SEPARATOR . $name . '.php', $generator->generateController());
      file_put_contents($contentPath . DIRECTORY_SEPARATOR . $contentName, $generator->generateHtml());
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
