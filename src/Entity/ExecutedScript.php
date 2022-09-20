<?php

namespace Drenso\DeployerBundle\Entity;

use DateTimeImmutable;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\Table;

#[Table(name: '_drenso_deployer_script')]
#[Entity]
class ExecutedScript
{
  public function __construct(
      #[Column]
      public readonly string $script,
      #[Column]
      public readonly DateTimeImmutable $executedAt
  ) {
  }
}
