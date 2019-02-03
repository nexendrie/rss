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
    "dateTimeFormat" => "Y-m-d H:i:s",
  ];
  
  /**
   * @throws \Nette\Utils\AssertionException
   */
  public function loadConfiguration(): void {
    $config = $this->getConfig($this->defaults);
    Validators::assertField($config, "shortenDescription", "integer");
    Validators::assertField($config, "dateTimeFormat", "string");
    $builder = $this->getContainerBuilder();
    $builder->addDefinition($this->prefix("generator"))
      ->setType(Generator::class)
      ->addSetup('$service->shortenDescription = ?', [$config["shortenDescription"]])
      ->addSetup('$service->dateTimeFormat = ?', [$config["dateTimeFormat"]]);
  }
}
?>