<?php
namespace Nexendrie\Rss;

use Tester\Assert;

require __DIR__ . "/../../bootstrap.php";

class GeneratorTest extends \Tester\TestCase {
  /** @var Generator */
  protected $generator;
  
  function setUp() {
    $this->generator = new Generator;
  }
  
  function testGetShortenDescription() {
    $result = $this->generator->shortenDescription;
    Assert::type("int", $result);
    Assert::same(150, $result);
  }
  
  /**
   * @param \SimpleXMLElement $channel
   * @return int
   */
  protected function countItems(\SimpleXMLElement $channel) {
    $items = 0;
    /** @var \SimpleXMLElement $child */
    foreach($channel->channel->children() as $child) {
      if($child->getName() === "item") $items++;
    }
    return $items;
  }
  
  function testEmptyChannel() {
    $this->generator->dataSource = function() {
      return [];
    };
    $result = $this->generator->generate();
    Assert::type(\SimpleXMLElement::class, $result);
    Assert::same("Test", (string) $result->channel->title);
    Assert::same("Test RSS Channel", (string) $result->channel->description);
    Assert::same(0, $this->countItems($result));
  }
  
  function testGenerate() {
    $this->generator->title = "Nexendrie RSS";
    $this->generator->description = "News for package nexendrie/rss";
    $this->generator->link = "https://gitlab.com/nexendrie/rss/";
    $this->generator->dataSource = function() {
      return [
        new RssChannelItem("Item 1", "Item 1 description", "", date($this->generator->dateTimeFormat))
      ];
    };
    $result = $this->generator->generate();
    Assert::type(\SimpleXMLElement::class, $result);
    Assert::same("Nexendrie RSS", (string) $result->channel->title);
    Assert::same("News for package nexendrie/rss", (string) $result->channel->description);
    Assert::same("https://gitlab.com/nexendrie/rss/", (string) $result->channel->link);
    Assert::same(1, $this->countItems($result));
  }
  
  function testInvalidDataSource() {
    $this->generator->dataSource = function() {
      return [
        new \stdClass
      ];
    };
    Assert::exception(function() {
      $this->generator->generate();
    }, \InvalidArgumentException::class, "The item is not of type " . RssChannelItem::class);
  }
  
  function testShortenDescription() {
    $description = str_repeat("ABDEFGH", 20);
    $this->generator->dataSource = function() use($description) {
      return [
        new RssChannelItem("Item 1", $description, "", date($this->generator->dateTimeFormat))
      ];
    };
    $this->generator->shortenDescription = 0;
    $result = $this->generator->generate();
    Assert::same($description, (string) $result->channel->item->description);
    $this->generator->shortenDescription = 10;
    $result = $this->generator->generate();
    Assert::same(13, (strlen((string) $result->channel->item->description)));
    $this->generator->shortenDescription = 250;
    $result = $this->generator->generate();
    Assert::same(strlen($description), (strlen((string) $result->channel->item->description)));
  }
  
  function testResponse() {
    Assert::exception(function() {
      $this->generator->response();
    }, InvalidStateException::class);
    $this->generator->title = "Nexendrie RSS";
    $this->generator->description = "News for package nexendrie/rss";
    $this->generator->link = "https://gitlab.com/nexendrie/rss/";
    $this->generator->dataSource = function() {
      return [
        new RssChannelItem("Item 1", "Item 1 description", "", date($this->generator->dateTimeFormat))
      ];
    };
    $result = $this->generator->response();
    Assert::type(RssResponse::class, $result);
    Assert::type(\SimpleXMLElement::class, $result->source);
  }
}

$test = new GeneratorTest;
$test->run();
?>