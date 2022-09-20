<?php

namespace Drenso\DeployerBundle\Scripts;

use Drenso\DeployerBundle\Enum\RunTypeEnum;

interface DeploymentScriptInterface
{
  /** Defines the run type of the script. */
  public function getRunType(): RunTypeEnum;

  /** Defines whether the script should only be run once, or always */
  public function runOnce(): bool;

  /** The actual run implementation */
  public function run(): int;
}
