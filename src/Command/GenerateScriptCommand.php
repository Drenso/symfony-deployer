<?php

namespace Drenso\DeployerBundle\Command;

use DateTimeImmutable;
use DateTimeZone;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\Exception\ValidationFailedException;
use Symfony\Component\Validator\Validation;
use Twig\Environment;

class GenerateScriptCommand extends Command
{
  public function __construct(
      private readonly string $scriptPath,
      private readonly string $namespace,
      private readonly ?Environment $twig)
  {
    parent::__construct();
  }

  protected function configure(): void
  {
    $this
        ->setDescription('Generate a new deployment script')
        ->addArgument('script-name', mode: InputArgument::OPTIONAL)
        ->addOption('run-once', mode: InputOption::VALUE_NONE|InputOption::VALUE_NEGATABLE)
        ->addOption('post', mode: InputOption::VALUE_NONE)
        ->addOption('pre', mode: InputOption::VALUE_NONE);
  }

  public function run(InputInterface $input, OutputInterface $output): int
  {
    $io = new SymfonyStyle($input, $output);

    if (!$this->twig) {
      $io->error('This command requires Twig te be installed.');

      return Command::FAILURE;
    }

    if ($input->getOption('post') && $input->getOption('pre')) {
      $io->error('Both post and pre options supplied');

      return Command::INVALID;
    }

    $scriptNameValidator = Validation::createCallable(new Regex(
        pattern: '/^[A-Z][a-zA-Z0-9_\x80-\xff]*$/',
        message: 'The script name must be a valid class name'
    ));

    $scriptName = $input->getArgument('script-name');
    try {
      $scriptNameValidator($scriptName);
    } catch (ValidationFailedException) {
      $scriptName = null;
    }

    if (!$scriptName) {
      $scriptName = $io->ask(
          'Provide a script name',
          validator: $scriptNameValidator);
    }

    $runOnce = $input->getOption('run-once') ?? $io->askQuestion(new ConfirmationQuestion('Should the script only be run once?'));

    if ($input->getOption('post')) {
      $runType = 'post';
    } elseif ($input->getOption('pre')) {
      $runType = 'pre';
    } else {
      $runType = $io->askQuestion(new ChoiceQuestion('When should it be run?', [
          'post' => 'Post deploy',
          'pre'  => 'Pre deploy',
      ], 'post'));
    }

    $scriptFile = $this->scriptPath . DIRECTORY_SEPARATOR . $scriptName . '.php';
    if (file_exists($scriptFile)) {
      $io->error('Script name already in use!');

      return Command::FAILURE;
    }

    $scriptContent = $this->twig->render('@DrensoDeployer/templates/ScriptTemplate.php.twig', [
        'namespace'  => $this->namespace,
        'scriptName' => $scriptName,
        'runOnce'    => $runOnce,
        'runType'    => $runType,
        'timestamp'  => (new DateTimeImmutable('now', new DateTimeZone('UTC')))->format('c'),
    ]);

    if (!file_exists($this->scriptPath)) {
      mkdir($this->scriptPath, 0755, true);
    }
    file_put_contents($scriptFile, $scriptContent);

    $io->success(sprintf('Generated new deployment script at %s', $scriptFile));

    return Command::SUCCESS;
  }
}
