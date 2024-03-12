<?php

namespace Drenso\DeployerBundle\Command;

use Drenso\DeployerBundle\Enum\RunTypeEnum;
use Drenso\DeployerBundle\Executor\ScriptExecutor;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class RunPreDeploymentTasksCommand extends Command
{
  public function __construct(private readonly ScriptExecutor $executor)
  {
    parent::__construct();
  }

  protected function configure(): void
  {
    $this->setDescription('Run configured pre deployment tasks');
  }

  public function run(InputInterface $input, OutputInterface $output): int
  {
    return $this->executor->run($this->getApplication(), $input, $output, RunTypeEnum::PRE);
  }
}
