<?php
declare(strict_types=1);

namespace Nexendrie\Rss;

use Tester\Assert,
    Nette\Application\Application,
    Nette\Application\Request,
    Nette\Application\IResponse;

require __DIR__ . "/../../bootstrap.php";

final class RssResponseTest extends \Tester\TestCase {
  use \Testbench\TCompiledContainer;
  
  /** @var \Nexendrie\Rss\Generator */
  protected $generator;
  
  public function setUp() {
    $this->generator = $this->getService(Generator::class);
    $this->generator->dataSource = function() {
      return new Collection;
    };
  }
  
  protected function checkRss($destination, array $params = [], array $post = []): string {
    $destination = ltrim($destination, ':');
    $pos = strrpos($destination, ':') ?: strlen($destination);
    $presenter = substr($destination, 0, $pos);
    $action = substr($destination, $pos + 1) ?: 'default';
    $params = ["action" => $action] + $params;
    /** @var Application $application */
    $application = $this->getService(Application::class);
    $request = new Request($presenter, "GET", $params, $post);
    $application->onResponse[] = function(Application $application, IResponse $response) {
      /** @var RssResponse $response */
      Assert::type(RssResponse::class, $response);
      Assert::type("string", $response->source);
    };
    ob_start();
    $application->processRequest($request);
    $response = ob_get_clean();
    Assert::type("string", $response);
    $xml = \Tester\DomQuery::fromXml($response);
    Assert::same("rss", $xml->getName(), "root element is");
    $channel = $xml->children();
    Assert::same("channel", $channel->getName(), "child of \"rss\"");
    Assert::same("title", $channel->children()->getName(), "child of \"channel\"");
    return $response;
  }
  
  public function testSend() {
    $this->checkRss("Rss:default");
  }
}

$test = new RssResponseTest;
$test->run();
?>