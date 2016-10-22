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
}

$test = new GeneratorTest;
$test->run();
?>