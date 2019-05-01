<?php
declare(strict_types=1);

namespace Nexendrie\Rss\Bridges\NetteDI;

use Nette\DI\CompilerExtension;
use Nexendrie\Rss\Generator;
use Nette\Schema\Expect;

/**
 * RssExtension for Nette DI Container
 *
 * @author Jakub Konečný
 */
final class RssExtension extends CompilerExtension {
  protected function setProperty(\Nette\DI\ServiceDefinition &$generator, \stdClass $config, string $property): void {
    if($config->$property !== "") {
      $generator->addSetup('$service->' . $property . " = ?", [$config->$property]);
    }
  }

  public function getConfigSchema(): \Nette\Schema\Schema {
    return Expect::structure([
      "shortenDescription" => Expect::int(150),
      "dateTimeFormat" => Expect::string(""),
      "template" => Expect::string(""),
    ]);
  }

  /**
   * @throws \Nette\Utils\AssertionException
   */
  public function loadConfiguration(): void {
    /** @var \stdClass $config */
    $config = $this->getConfig();
    $builder = $this->getContainerBuilder();
    $generator = $builder->addDefinition($this->prefix("generator"))
      ->setType(Generator::class)
      ->addSetup('$service->shortenDescription = ?', [$config->shortenDescription]);
    $this->setProperty($generator, $config, "dateTimeFormat");
    $this->setProperty($generator, $config, "template");
  }
}
?>