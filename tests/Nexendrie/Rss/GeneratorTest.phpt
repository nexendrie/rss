<?php
declare(strict_types=1);

namespace Nexendrie\Rss;

use Tester\Assert;

require __DIR__ . "/../../bootstrap.php";

class GeneratorTest extends \Tester\TestCase {
  /** @var Generator */
  protected $generator;
  
  public function setUp() {
    $this->generator = new Generator;
  }
  
  public function testGetShortenDescription() {
    $result = $this->generator->shortenDescription;
    Assert::type("int", $result);
    Assert::same(150, $result);
  }
  
  public function testGetLastBuildDate() {
    $lastBuildDate = $this->generator->lastBuildDate;
    Assert::type("callable", $lastBuildDate);
  }
  
  protected function countItems(\SimpleXMLElement $channel): int {
    $items = 0;
    /** @var \SimpleXMLElement $child */
    foreach($channel->channel->children() as $child) {
      if($child->getName() === "item") {
        $items++;
      }
    }
    return $items;
  }
  
  public function testEmptyChannel() {
    $this->generator->dataSource = function() {
      return new Collection();
    };
    $result = $this->generator->generate();
    Assert::type(\SimpleXMLElement::class, $result);
    Assert::same("Test", (string) $result->channel->title);
    Assert::same("Test RSS Channel", (string) $result->channel->description);
    Assert::same(0, $this->countItems($result));
  }
  
  public function testGenerate() {
    $this->generator->title = $title = "Nexendrie RSS";
    Assert::same($title, $this->generator->title);
    $this->generator->description = $description = "News for package nexendrie/rss";
    Assert::same($description, $this->generator->description);
    $this->generator->link = $link = "https://gitlab.com/nexendrie/rss/";
    Assert::same($link, $this->generator->link);
    $this->generator->dataSource = function() {
      $pubDate = date($this->generator->dateTimeFormat);
      $items = new Collection;
      $items[] = new RssChannelItem("Item 1", "Item 1 description", "", $pubDate);
      return $items;
    };
    $result = $this->generator->generate();
    Assert::type(\SimpleXMLElement::class, $result);
    Assert::same($title, (string) $result->channel->title);
    Assert::same($description, (string) $result->channel->description);
    Assert::same($link, (string) $result->channel->link);
    Assert::same(1, $this->countItems($result));
    Assert::type("string", (string) $result->channel->lastBuidDate);
  }
  
  public function testInvalidDataSource() {
    $this->generator->dataSource = function() {
      return [];
    };
    Assert::exception(function() {
      $this->generator->generate();
    }, \InvalidArgumentException::class, "Callback for data source for RSS generator has to return " . Collection::class . ".");
  }
  
  public function testShortenDescription() {
    $description = str_repeat("ABDEFGH", 20);
    $this->generator->dataSource = function() use($description) {
      $pubDate = date($this->generator->dateTimeFormat);
      $items = new Collection;
      $items[] = new RssChannelItem("Item 1", $description, "", $pubDate);
      return $items;
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
  
  public function testCustomLastBuildDate() {
    $this->generator->title = "Nexendrie RSS";
    $this->generator->description = "News for package nexendrie/rss";
    $this->generator->link = "https://gitlab.com/nexendrie/rss/";
    $this->generator->dataSource = function() {
      $pubDate = date($this->generator->dateTimeFormat);
      $items = new Collection;
      $items[] = new RssChannelItem("Item 1", "Item 1 description", "", $pubDate);
      return $items;
    };
    $this->generator->lastBuildDate = function() {
      return time();
    };
    $result = $this->generator->generate();
    Assert::type("string", (string) $result->channel->lastBuidDate);
  }
  
  public function testInvalidLastBuildDate() {
    $this->generator->title = "Nexendrie RSS";
    $this->generator->description = "News for package nexendrie/rss";
    $this->generator->link = "https://gitlab.com/nexendrie/rss/";
    $this->generator->dataSource = function() {
      $pubDate = date($this->generator->dateTimeFormat);
      $items = new Collection;
      $items[] = new RssChannelItem("Item 1", "Item 1 description", "", $pubDate);
      return $items;
    };
    $this->generator->lastBuildDate = function() {
      return "abc";
    };
    Assert::exception(function() {
      $this->generator->generate();
    }, \InvalidArgumentException::class, "Callback for last build date for RSS generator has to return integer.");
  }
  
  public function testResponse() {
    Assert::exception(function() {
      $this->generator->response();
    }, InvalidStateException::class);
    $this->generator->title = "Nexendrie RSS";
    $this->generator->description = "News for package nexendrie/rss";
    $this->generator->link = "https://gitlab.com/nexendrie/rss/";
    $this->generator->dataSource = function() {
      $pubDate = date($this->generator->dateTimeFormat);
      $items = new Collection;
      $items[] = new RssChannelItem("Item 1", "Item 1 description", "", $pubDate);
      return $items;
    };
    $result = $this->generator->response();
    Assert::type(RssResponse::class, $result);
    Assert::type(\SimpleXMLElement::class, $result->source);
  }
}

$test = new GeneratorTest;
$test->run();
?>