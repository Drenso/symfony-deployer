<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use Rector\TypeDeclaration\Rector\BooleanAnd\BinaryOpNullableToInstanceofRector;

return RectorConfig::configure()
  ->withPaths(['./src'])
  ->withImportNames()
  ->withPhpSets()
  ->withPreparedSets(
    typeDeclarations: true,
  )
  ->withSkip([
    BinaryOpNullableToInstanceofRector::class, // Makes code unreadable
  ]);
