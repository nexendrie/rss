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
    "shortenDescription" => 150
  ];
  
  function loadConfiguration() {
    $config = $this->getConfig($this->defaults);
    Validators::assertField($config, "shortenDescription", "integer");
    $builder = $this->getContainerBuilder();
    $builder->addDefinition($this->prefix("generator"))
      ->setClass(\Nexendrie\Rss\Generator::class)
      ->addSetup("setShortenDescription", [$config["shortenDescription"]]);
  }
}
?>