<?php
namespace Nexendrie\Rss\Bridges\NetteDI;

use Nette\DI\CompilerExtension,
    Nette\Utils\Validators;

/**
 * RssExtension for Nette DI Container
 *
 * @author Jakub Konečný
 */
class RssExtension extends CompilerExtension {
  /** @var array */
  protected $defaults = [
    "shortenDescription" => 150,
    "dateTimeFormat" => "Y-m-d H:i:s",
  ];
  
  function loadConfiguration() {
    $config = $this->getConfig($this->defaults);
    Validators::assertField($config, "shortenDescription", "integer");
    Validators::assertField($config, "dateTimeFormat", "string");
    $builder = $this->getContainerBuilder();
    $builder->addDefinition($this->prefix("generator"))
      ->setClass(\Nexendrie\Rss\Generator::class)
      ->addSetup("setShortenDescription", [$config["shortenDescription"]])
      ->addSetup("setDateTimeFormat", [$config["dateTimeFormat"]]);
  }
}
?>