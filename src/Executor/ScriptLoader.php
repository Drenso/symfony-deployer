<?php

namespace Drenso\DeployerBundle\Executor;

use ReflectionClass;
use ReflectionException;

class ScriptLoader
{
  public function __construct(private readonly string $namespace)
  {
  }

  protected static function requireOnce(string $path): void
  {
    require_once $path;
  }

  /**
   * @throws ReflectionException
   *
   * @return class-string[]
   */
  public function loadScripts(array $scriptFiles): array
  {
    $includedFiles = [];
    foreach ($scriptFiles as $scriptFile) {
      static::requireOnce($scriptFile);

      $realScriptFile = realpath($scriptFile);
      assert($realScriptFile !== false);

      $includedFiles[] = $realScriptFile;
    }

    $classes = $this->loadMigrationClasses($includedFiles);
    $scripts = [];
    foreach ($classes as $class) {
      $scripts[] = $class->getName();
    }

    return $scripts;
  }

  /**
   * Look up all declared classes and find those classes contained
   * in the given `$files` array.
   *
   * @param string[] $files The set of files that were `required`
   *
   * @throws ReflectionException
   *
   * @return ReflectionClass<object>[] the classes in `$files`
   */
  protected function loadMigrationClasses(array $files): array
  {
    $classes = [];
    foreach (get_declared_classes() as $class) {
      $reflectionClass = new ReflectionClass($class);

      if (!in_array($reflectionClass->getFileName(), $files, true)) {
        continue;
      }

      if (!$this->isReflectionClassInNamespace($reflectionClass)) {
        continue;
      }

      $classes[] = $reflectionClass;
    }

    return $classes;
  }

  private function isReflectionClassInNamespace(ReflectionClass $reflectionClass): bool
  {
    return strncmp($reflectionClass->getName(), $this->namespace . '\\', strlen($this->namespace) + 1) === 0;
  }
}
