<?php

namespace Drenso\DeployerBundle\Executor;

use FilesystemIterator;
use InvalidArgumentException;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use RegexIterator;

class ScriptFinder
{
  public function __construct(private readonly string $scriptPath)
  {
  }

  /** @return string[] */
  public function find(): array
  {
    return $this->getMatches($this->createIterator($this->getRealPath($this->scriptPath)));
  }

  protected function getRealPath(string $directory): string
  {
    $dir = realpath($directory);

    if ($dir === false || !is_dir($dir)) {
      throw new InvalidArgumentException(
          sprintf('Cannot load migrations from "%s" because it is not a valid directory', $directory)
      );
    }

    return $dir;
  }

  private function createIterator(string $dir): RegexIterator
  {
    return new RegexIterator(
        new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($dir, FilesystemIterator::SKIP_DOTS|FilesystemIterator::FOLLOW_SYMLINKS),
            RecursiveIteratorIterator::LEAVES_ONLY
        ),
        sprintf(
            '#^.+\\%s[^\\%s]+\\.php$#i',
            DIRECTORY_SEPARATOR,
            DIRECTORY_SEPARATOR
        ),
        RegexIterator::GET_MATCH
    );
  }

  /** @return string[] */
  private function getMatches(RegexIterator $iteratorFilesMatch): array
  {
    $files = [];
    foreach ($iteratorFilesMatch as $file) {
      $files[] = $file[0];
    }

    return $files;
  }
}
