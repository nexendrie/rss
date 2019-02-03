<?php
declare(strict_types=1);

namespace Nexendrie\Rss\Bridges\NetteDI;

use Nette\DI\CompilerExtension;
use Nette\Utils\Validators;
use Nexendrie\Rss\Generator;

/**
 * RssExtension for Nette DI Container
 *
 * @author Jakub Konečný
 */
final class RssExtension extends CompilerExtension {
  /** @var array */
  protected $defaults = [
    "shortenDescription" => 150,
    "dateTimeFormat" => "",
    "template" => "",
  ];

  protected function setProperty(\Nette\DI\ServiceDefinition &$generator, array $config, string $property): void {
    if($config[$property] !== "") {
      $generator->addSetup('$service->' . $property . " = ?", [$config[$property]]);
    }
  }

  /**
   * @throws \Nette\Utils\AssertionException
   */
  public function loadConfiguration(): void {
    $config = $this->getConfig($this->defaults);
    Validators::assertField($config, "shortenDescription", "integer");
    Validators::assertField($config, "dateTimeFormat", "string");
    Validators::assertField($config, "template", "string");
    $builder = $this->getContainerBuilder();
    $generator = $builder->addDefinition($this->prefix("generator"))
      ->setType(Generator::class)
      ->addSetup('$service->shortenDescription = ?', [$config["shortenDescription"]]);
    $this->setProperty($generator, $config, "dateTimeFormat");
    $this->setProperty($generator, $config, "template");
  }
}
?>