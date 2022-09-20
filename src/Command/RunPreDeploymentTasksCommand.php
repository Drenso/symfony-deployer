<?php

namespace Drenso\DeployerBundle\Command;

use Drenso\DeployerBundle\Enum\RunTypeEnum;
use Drenso\DeployerBundle\Executor\ScriptExecutor;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'drenso:deployer:pre',
    description: 'Run configured pre deployment tasks',
)]
class RunPreDeploymentTasksCommand extends Command
{
  public function __construct(private readonly ScriptExecutor $executor)
  {
    parent::__construct();
  }

  public function run(InputInterface $input, OutputInterface $output): int
  {
    return $this->executor->run($this->getApplication(), $input, $output, RunTypeEnum::PRE);
  }
}
