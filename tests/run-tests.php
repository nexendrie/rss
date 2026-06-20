#!/usr/bin/env php
<?php
declare(strict_types=1);

require __DIR__ . "/../vendor/autoload.php";
require __DIR__ . "/../vendor/konecnyjakub/mytester/src/functions.php";

use MyTester\Bridges\NetteDI\ContainerFactory;
use MyTester\Tester;
use Nette\Bootstrap\Configurator;
use Nette\Utils\FileSystem;

ContainerFactory::$tempDir = __DIR__ . "/temp";
FileSystem::createDir(ContainerFactory::$tempDir);
ContainerFactory::$onCreate = static function (Configurator $configurator): void {
    $configurator->addStaticParameters([
        "appDir" => __DIR__,
    ]);
    $configurator->addConfig(__DIR__ . "/tests.neon");
};
$container = ContainerFactory::create(true);
/** @var Tester $runner */
$runner = $container->getByType(Tester::class);
$runner->execute();
