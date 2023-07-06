<?php

declare(strict_types=1);

use ComposerUnused\ComposerUnused\Configuration\Configuration;
use ComposerUnused\ComposerUnused\Configuration\NamedFilter;
use Webmozart\Glob\Glob;

return static function (Configuration $config): Configuration {
  return $config
      ->setAdditionalFilesFor('drenso/symfony-deployer-bundle', [
          __FILE__,
          ...Glob::glob(__DIR__ . '/src/**/*.php'),
      ]);
};
