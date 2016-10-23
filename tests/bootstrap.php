<?php
require __DIR__ . "/../vendor/autoload.php";

class RssPresenter extends \Nette\Application\UI\Presenter {
  /** @var \Nexendrie\Rss\Generator @inject */
  public $generator;
  
  function renderDefault() {
    $this->generator->dataSource = function() {
      return [];
    };
    $this->sendResponse(new \Nexendrie\Rss\RssResponse($this->generator->generate()));
  }
}

Testbench\Bootstrap::setup(__DIR__ . '/_temp', function (\Nette\Configurator $configurator) {
  $configurator->addParameters(["appDir" => __DIR__,]);
  $configurator->addConfig(__DIR__ . "/tests.neon");
});
?>