<?php

namespace Drenso\DeployerBundle\Scripts;

use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Drenso\DeployerBundle\Enum\RunTypeEnum;
use Drenso\DeployerBundle\Exception\SkipScript;
use RuntimeException;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Contracts\Service\ServiceProviderInterface;

abstract class DeploymentScript
{
  /** @phpstan-ignore missingType.generics */
  final public function __construct(
    private readonly ServiceProviderInterface $services,
    private readonly ParameterBagInterface $parameters,
    private readonly Application $application,
    private readonly ?MessageBusInterface $messageBus,
    protected readonly EntityManagerInterface $entityManager,
    protected readonly OutputInterface $output)
  {
  }

  /**
   * Returns a service provider containing the services tagged with
   * the \Drenso\DeployerBundle\DependencyInjection\DrensoDeployerExtension::TAG_DEPENDENCY tag.
   *
   * @phpstan-ignore missingType.generics
   */
  protected function services(): ServiceProviderInterface
  {
    return $this->services;
  }

  protected function getParameter(string $name): mixed
  {
    return $this->parameters->get($name);
  }

  /**
   * Shortcut method to run a console command.
   *
   * @phpstan-ignore missingType.iterableValue
   */
  protected function executeConsoleCommand(string $commandName, array $arguments = []): int
  {
    return $this->application
      ->find($commandName)
      ->run(new ArrayInput($arguments), $this->output);
  }

  /** Dispatches a Symfony messenger message */
  protected function dispatchMessage(object $msg): void
  {
    if (!$this->messageBus) {
      throw new RuntimeException('Message bus not available');
    }

    $this->messageBus->dispatch($msg);
  }

  protected function skipIf(bool $condition, string $message = 'Unknown reason'): void
  {
    if ($condition) {
      throw new SkipScript($message);
    }
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
