<?php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

$symfonyVarDumper = __DIR__ . '/../vendor/symfony/symfony/src/Symfony/Component/VarDumper/Resources/functions/dump.php';
$componentVarDumper = __DIR__ . '/../vendor/symfony/var-dumper/Resources/functions/dump.php';

require_once \file_exists($symfonyVarDumper) ? $symfonyVarDumper : $componentVarDumper;
