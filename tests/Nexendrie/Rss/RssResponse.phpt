<?php
declare(strict_types=1);

namespace Nexendrie\Rss;

use Tester\Assert;

require __DIR__ . "/../../bootstrap.php";


class RssResponseTest extends \Tester\TestCase {
  use \Testbench\TCompiledContainer;
  use \Testbench\TPresenter;
  
  /** @var \Nexendrie\Rss\Generator */
  protected $generator;
  
  function setUp() {
    $this->generator = $this->getService(Generator::class);
    $this->generator->dataSource = function() {
      return [];
    };
  }
  
  function testGetSource() {
    $response = $this->generator->response();
    Assert::type(\SimpleXMLElement::class, $response->source);
  }
  
  /**
   * @param $destination
   * @param array $params
   * @param array $post
   * @return RssResponse
   */
  protected function checkRssChannel($destination, $params = [], $post = []) {
    /** @var RssResponse $response */
    $response = $this->check($destination, $params, $post);
    if(!$this->__testbench_exception) {
      Assert::same(200, $this->getReturnCode());
      Assert::type(RssResponse::class, $response);
      Assert::type(\SimpleXMLElement::class, $response->source);
      $xml = \Tester\DomQuery::fromXml($response->getSource()->asXML());
      Assert::same("rss", $xml->getName(), "root element is");
      $channel = $xml->children();
      Assert::same("channel", $channel->getName(), "child of \"rss\"");
      Assert::same("title", $channel->children()->getName(), "child of \"channel\"");
    }
    return $response;
  }
  
  function testSend() {
    $this->checkRssChannel("Rss:default");
  }
}

$test = new RssResponseTest;
$test->run();
?>