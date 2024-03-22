<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;

return RectorConfig::configure()
  ->withPaths(['./src'])
  ->withImportNames()
  ->withPhpSets()
  ->withPreparedSets(
    typeDeclarations: true,
  );
