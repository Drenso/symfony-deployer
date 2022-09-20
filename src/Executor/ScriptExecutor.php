<?php

namespace Drenso\DeployerBundle\Executor;

use DateTimeImmutable;
use DateTimeZone;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectRepository;
use Drenso\DeployerBundle\Entity\ExecutedDeploymentScript;
use Drenso\DeployerBundle\Enum\RunTypeEnum;
use Drenso\DeployerBundle\Scripts\DeploymentScript;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\DependencyInjection\ServiceLocator;

class ScriptExecutor
{
  private ?ObjectRepository $repo = null;

  public function __construct(
      private readonly ScriptFinder $finder,
      private readonly ScriptLoader $loader,
      private readonly ServiceLocator $serviceLocator,
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

    $instances = [];
    foreach ($scripts as $script) {
      // Instantiate all scripts
      $scriptInstance = new $script(
          $this->serviceLocator,
          $application,
          $output
      );
      if (!$scriptInstance instanceof DeploymentScript) {
        continue;
      }

      if ($scriptInstance->getRunType() !== $runType) {
        continue;
      }

      $instances[] = $scriptInstance;
    }

    if (empty($instances)) {
      $io->info('No deployment scripts registered');

      return Command::SUCCESS;
    }

    // Sort scripts
    usort($instances, fn (DeploymentScript $a, DeploymentScript $b) => $a->timestamp() <=> $b->timestamp());

    // Run the scripts
    foreach ($instances as $instance) {
      $io->section($instance::class);

      $dbScript = $this->getDatabaseInstance($instance);

      if ($dbScript && !$dbScript->shouldRun($instance)) {
        $io->note('Already done, skipped!');

        continue;
      }

      $io->comment(sprintf('Running %s...', $instance::class));
      if (($returnCode = $instance->run()) !== Command::SUCCESS) {
        $io->error(sprintf('Non-zero (%d) return code received from %s', $returnCode, $instance::class));

        return Command::FAILURE;
      }

      if ($instance->runOnce()) {
        if ($dbScript) {
          $this->em->remove($dbScript);
          $this->em->flush();
        }
        $this->em->persist(new ExecutedDeploymentScript($instance::class, new DateTimeImmutable('now', new DateTimeZone('UTC'))));
        $this->em->flush();
      }

      $io->success(sprintf('%s completed successfully!', $instance::class));
    }

    return Command::SUCCESS;
  }

  private function getDatabaseInstance(DeploymentScript $script): ?ExecutedDeploymentScript
  {
    if (!$script->runOnce()) {
      return null;
    }

    $this->repo ??= $this->em->getRepository(ExecutedDeploymentScript::class);

    return $this->repo->find($script::class);
  }
}
