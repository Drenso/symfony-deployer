<?php

namespace Drenso\DeployerBundle\Scripts;

use Drenso\DeployerBundle\Enum\RunTypeEnum;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

abstract class DeploymentScript implements DeploymentScriptInterface
{
  final public function __construct(
      private readonly ContainerInterface $container,
      private readonly Application $application,
      private readonly OutputInterface $output)
  {
  }

  /** Default to post run type */
  public function getRunType(): RunTypeEnum
  {
    return RunTypeEnum::POST;
  }

  /** Default to run script once */
  public function runOnce(): bool
  {
    return true;
  }

  /** Can be used to get services from the container (which must be public) */
  protected function getContainer(): ContainerInterface
  {
    return $this->container;
  }

  /** Shortcut method to run a console command */
  protected function executeConsoleCommand(string $commandName, array $arguments = []): int
  {
    return $this->application
        ->find($commandName)
        ->run(new ArrayInput($arguments), $this->output);
  }
}
