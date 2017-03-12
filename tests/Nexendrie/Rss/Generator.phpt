<?php
declare(strict_types=1);

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
  
  function testGetLastBuildDate() {
    $lastBuildDate = $this->generator->lastBuildDate;
    Assert::type("callable", $lastBuildDate);
  }
  
  /**
   * @param \SimpleXMLElement $channel
   * @return int
   */
  protected function countItems(\SimpleXMLElement $channel) {
    $items = 0;
    /** @var \SimpleXMLElement $child */
    foreach($channel->channel->children() as $child) {
      if($child->getName() === "item") {
        $items++;
      }
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
    $this->generator->title = $title = "Nexendrie RSS";
    $this->generator->description = $description = "News for package nexendrie/rss";
    $this->generator->link = $link = "https://gitlab.com/nexendrie/rss/";
    $this->generator->dataSource = function() {
      $pubDate = date($this->generator->dateTimeFormat);
      return [
        new RssChannelItem("Item 1", "Item 1 description", "", $pubDate)
      ];
    };
    $result = $this->generator->generate();
    Assert::type(\SimpleXMLElement::class, $result);
    Assert::same($title, (string) $result->channel->title);
    Assert::same($description, (string) $result->channel->description);
    Assert::same($link, (string) $result->channel->link);
    Assert::same(1, $this->countItems($result));
    Assert::type("string", (string) $result->channel->lastBuidDate);
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
      $pubDate = date($this->generator->dateTimeFormat);
      return [
        new RssChannelItem("Item 1", $description, "", $pubDate)
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
  
  function testCustomLastBuildDate() {
    $this->generator->title = "Nexendrie RSS";
    $this->generator->description = "News for package nexendrie/rss";
    $this->generator->link = "https://gitlab.com/nexendrie/rss/";
    $this->generator->dataSource = function() {
      $pubDate = date($this->generator->dateTimeFormat);
      return [
        new RssChannelItem("Item 1", "Item 1 description", "", $pubDate)
      ];
    };
    $this->generator->lastBuildDate = function() {
      return time();
    };
    $result = $this->generator->generate();
    Assert::type("string", (string) $result->channel->lastBuidDate);
    $this->generator->lastBuildDate = time();
    $result = $this->generator->generate();
    Assert::type("string", (string) $result->channel->lastBuidDate);
  }
  
  function testInvalidLastBuildDate() {
    $this->generator->title = "Nexendrie RSS";
    $this->generator->description = "News for package nexendrie/rss";
    $this->generator->link = "https://gitlab.com/nexendrie/rss/";
    $this->generator->dataSource = function() {
      $pubDate = date($this->generator->dateTimeFormat);
      return [
        new RssChannelItem("Item 1", "Item 1 description", "", $pubDate)
      ];
    };
    $this->generator->lastBuildDate = "abc";
    Assert::exception(function() {
      $this->generator->generate();
    }, \InvalidArgumentException::class, "Last build date for RSS generator has to be callback or integer.");
    $this->generator->lastBuildDate = function() {
      return "abc";
    };
    Assert::exception(function() {
      $this->generator->generate();
    }, \InvalidArgumentException::class, "Callback for last build date for RSS generator has to return integer.");
  }
  
  function testResponse() {
    Assert::exception(function() {
      $this->generator->response();
    }, InvalidStateException::class);
    $this->generator->title = "Nexendrie RSS";
    $this->generator->description = "News for package nexendrie/rss";
    $this->generator->link = "https://gitlab.com/nexendrie/rss/";
    $this->generator->dataSource = function() {
      $pubDate = date($this->generator->dateTimeFormat);
      return [
        new RssChannelItem("Item 1", "Item 1 description", "", $pubDate)
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