<?php

namespace Drenso\DeployerBundle\Entity;

use DateTimeImmutable;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\Table;
use Drenso\DeployerBundle\Scripts\DeploymentScript;
use InvalidArgumentException;

#[Table(name: '_drenso_deployer_scripts')]
#[Entity]
class ExecutedDeploymentScript
{
  public function __construct(
    #[Id]
    #[Column(unique: true)]
    public readonly string $script,
    #[Column]
    public readonly DateTimeImmutable $executedAt)
  {
  }

  public function shouldRun(DeploymentScript $instance): bool
  {
    if ($instance::class !== $this->script) {
      throw new InvalidArgumentException(sprintf(
        'Supplied instance `%s` does not match with record for `%s`',
        $instance::class,
        $this->script,
      ));
    }

    return $instance->timestamp() > $this->executedAt;
  }
}
