<?php

namespace Drenso\DeployerBundle\Executor;

use Doctrine\ORM\EntityManagerInterface;
use Drenso\DeployerBundle\Enum\RunTypeEnum;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class ScriptExecutor
{
  public function __construct(
      private readonly ScriptFinder $finder,
      private readonly ScriptLoader $loader,
      private readonly EntityManagerInterface $em)
  {
  }

  public function run(
      Application $application,
      InputInterface $input,
      OutputInterface $output,
      RunTypeEnum $runType): int
  {
    $io = new SymfonyStyle($input, $output);

    // Find and load the script files
    $scripts = $this->loader->loadScripts($this->finder->find());
    foreach ($scripts as $script) {
      $io->text($script);
    }

    return Command::SUCCESS;
  }
}
