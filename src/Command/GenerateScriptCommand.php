<?php

namespace Drenso\DeployerBundle\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\Validation;

#[AsCommand(
    name: 'drenso:deployer:generate',
    description: 'Generate a new deployment script'
)]
class GenerateScriptCommand extends Command
{
  public function __construct(
      private readonly string $scriptPath,
      private readonly string $namespace)
  {
    parent::__construct();
  }

  public function run(InputInterface $input, OutputInterface $output): int
  {
    $io = new SymfonyStyle($input, $output);

    $scriptName = $io->ask('Provide a script name', Validation::createCallable(new Regex(
        pattern: '/^[a-zA-Z_\x80-\xff][a-zA-Z0-9_\x80-\xff]*$/',
        message: 'The script name must be a valid class name'
    )));

    $scriptFile = realpath($this->scriptPath . DIRECTORY_SEPARATOR . $scriptName . '.php');
    if (file_exists($scriptFile)) {
      $io->error('Script name already in use!');

      return Command::FAILURE;
    }

    $scriptContent = file_get_contents(join(DIRECTORY_SEPARATOR, [
        dirname(__DIR__),
        'Resources',
        'templates',
        'ScriptTemplate.php.template',
    ]));
    $scriptContent = strtr($scriptContent, '{{namespace}}', $this->namespace);
    $scriptContent = strtr($scriptContent, '{{scriptName}}', $scriptName);

    file_put_contents($scriptFile, $scriptContent);

    $io->success(sprintf('Generated new deployment script at %s', $scriptFile));

    return Command::SUCCESS;
  }

}
