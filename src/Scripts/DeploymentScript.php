<?php

namespace Drenso\DeployerBundle\Scripts;

use DateTimeImmutable;
use Drenso\DeployerBundle\Enum\RunTypeEnum;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Contracts\Service\ServiceProviderInterface;

abstract class DeploymentScript
{
  final public function __construct(
      private readonly ServiceProviderInterface $services,
      private readonly Application $application,
      protected readonly OutputInterface $output)
  {
  }

  /** Returns a service provider containing the services tagged with the DrensoDeployerExtension::TAG_DEPENDENCY tag */
  protected function services(): ServiceProviderInterface
  {
    return $this->services;
  }

  /** Shortcut method to run a console command */
  protected function executeConsoleCommand(string $commandName, array $arguments = []): int
  {
    return $this->application
        ->find($commandName)
        ->run(new ArrayInput($arguments), $this->output);
  }

  /** Defines the run type of the script. */
  public function getRunType(): RunTypeEnum
  {
    return RunTypeEnum::POST;
  }

  /** Defines whether the script should only be run once, or always */
  public function runOnce(): bool
  {
    return true;
  }

  /** The actual run implementation */
  abstract public function run(): int;

  /** Timestamp, used for determining whether it needs to be executed again */
  abstract public function timestamp(): DateTimeImmutable;
}
