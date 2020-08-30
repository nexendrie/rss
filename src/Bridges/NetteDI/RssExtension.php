<?php
declare(strict_types=1);

namespace Nexendrie\Rss\Bridges\NetteDI;

use Nette\DI\CompilerExtension;
use Nette\DI\Definitions\ServiceDefinition;
use Nexendrie\Rss\Generator;
use Nette\Schema\Expect;
use Nexendrie\Rss\InvalidRssExtensionException;
use Nexendrie\Rss\IRssExtension;

/**
 * RssExtension for Nette DI Container
 *
 * @author Jakub Konečný
 * @method \stdClass getConfig()
 */
final class RssExtension extends CompilerExtension {
  /** @internal */
  public const SERVICE_GENERATOR = "generator";

  protected function setProperty(ServiceDefinition &$generator, \stdClass $config, string $property): void {
    if($config->$property !== "") {
      $generator->addSetup('$service->' . $property . " = ?", [$config->$property]);
    }
  }

  public function getConfigSchema(): \Nette\Schema\Schema {
    return Expect::structure([
      "shortenDescription" => Expect::int(150),
      "dateTimeFormat" => Expect::string(""),
      "template" => Expect::string(""),
      "extensions" => Expect::arrayOf("class")->default([]),
    ]);
  }

  public function loadConfiguration(): void {
    $config = $this->getConfig();
    $builder = $this->getContainerBuilder();
    $generator = $builder->addDefinition($this->prefix(static::SERVICE_GENERATOR))
      ->setType(Generator::class)
      ->addSetup('$service->shortenDescription = ?', [$config->shortenDescription]);
    $this->setProperty($generator, $config, "dateTimeFormat");
    $this->setProperty($generator, $config, "template");
    /** @var string $extension */
    foreach($config->extensions as $index => $extension) {
      if(!class_exists($extension) || !is_subclass_of($extension, IRssExtension::class)) {
        throw new InvalidRssExtensionException("Invalid RSS extension $extension.");
      }
      $builder->addDefinition($this->prefix("extension.$index"))
        ->setType($extension);
    }
  }

  public function beforeCompile() {
    $builder = $this->getContainerBuilder();
    /** @var ServiceDefinition $generator */
    $generator = $builder->getDefinition($this->prefix(static::SERVICE_GENERATOR));
    $extensions = $builder->findByType(IRssExtension::class);
    foreach($extensions as $extension) {
      $generator->addSetup('$service->extensions[] = ?', [$extension]);
    }
  }
}
?>