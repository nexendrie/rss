<?php
declare(strict_types=1);

namespace Nexendrie\Rss;

use Tester\Assert;

require __DIR__ . "/../../bootstrap.php";

final class GeneratorTest extends \Tester\TestCase {
  /** @var Generator */
  protected $generator;
  
  public function setUp() {
    $this->generator = new Generator();
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
    Assert::type("string", $result);
    $result = new \SimpleXMLElement($result);
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
      $items = new Collection();
      $items[] = new RssChannelItem("Item 1", "Item 1 description", "", 123);
      return $items;
    };
    $result = $this->generator->generate();
    Assert::type("string", $result);
    $result = new \SimpleXMLElement($result);
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
      $items = new Collection();
      $items[] = new RssChannelItem("Item 1", $description, "", 123);
      return $items;
    };
    $this->generator->shortenDescription = 0;
    $result = new \SimpleXMLElement($this->generator->generate());
    Assert::same($description, (string) $result->channel->item->description);
    $this->generator->shortenDescription = 10;
    $result = new \SimpleXMLElement($this->generator->generate());
    Assert::same(13, (strlen((string) $result->channel->item->description)));
    $this->generator->shortenDescription = 250;
    $result = new \SimpleXMLElement($this->generator->generate());
    Assert::same(strlen($description), (strlen((string) $result->channel->item->description)));
  }
  
  public function testCustomLastBuildDate() {
    $this->generator->title = "Nexendrie RSS";
    $this->generator->description = "News for package nexendrie/rss";
    $this->generator->link = "https://gitlab.com/nexendrie/rss/";
    $this->generator->dataSource = function() {
      $items = new Collection();
      $items[] = new RssChannelItem("Item 1", "Item 1 description", "", 123);
      return $items;
    };
    $this->generator->lastBuildDate = function() {
      return time();
    };
    $result = new \SimpleXMLElement($this->generator->generate());
    Assert::type("string", (string) $result->channel->lastBuidDate);
  }
  
  public function testInvalidLastBuildDate() {
    $this->generator->title = "Nexendrie RSS";
    $this->generator->description = "News for package nexendrie/rss";
    $this->generator->link = "https://gitlab.com/nexendrie/rss/";
    $this->generator->dataSource = function() {
      $items = new Collection();
      $items[] = new RssChannelItem("Item 1", "Item 1 description", "", 123);
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
      $items = new Collection();
      $items[] = new RssChannelItem("Item 1", "Item 1 description", "", 123);
      return $items;
    };
    $result = $this->generator->response();
    Assert::type(RssResponse::class, $result);
    Assert::type("string", $result->source);
  }

  public function testDateTimeFormat() {
    $dateTimeFormat = "Y/m/d";
    $this->generator->dateTimeFormat = $dateTimeFormat;
    Assert::same($dateTimeFormat, $this->generator->dateTimeFormat);
    $this->generator->title = "Nexendrie RSS";
    $this->generator->description = "News for package nexendrie/rss";
    $this->generator->link = "https://gitlab.com/nexendrie/rss/";
    $this->generator->dataSource = function() {
      return new Collection();
    };
    $result = new \SimpleXMLElement($this->generator->generate());
    Assert::same(date($dateTimeFormat), (string) $result->channel->lastBuildDate);
  }

  public function testOptionalThings() {
    $this->generator->title = "Nexendrie RSS";
    $this->generator->description = "News for package nexendrie/rss";
    $this->generator->link = "https://gitlab.com/nexendrie/rss/";
    $this->generator->dataSource = function() {
      return new Collection();
    };

    $language = "en";
    $this->generator->language = $language;
    Assert::same($language, $this->generator->language);
    $copyright = "Copyright 2019, Abc";
    $this->generator->copyright = $copyright;
    Assert::same($copyright, $this->generator->copyright);
    $managingEditor = "Abc";
    $this->generator->managingEditor = $managingEditor;
    Assert::same($managingEditor, $this->generator->managingEditor);
    $webMaster = "Def";
    $this->generator->webMaster = $webMaster;
    Assert::same($webMaster, $this->generator->webMaster);

    $result = new \SimpleXMLElement($this->generator->generate());
    Assert::same($language, (string) $result->channel->language);
    Assert::same($copyright, (string) $result->channel->copyright);
    Assert::same($managingEditor, (string) $result->channel->managingEditor);
    Assert::same($webMaster, (string) $result->channel->webMaster);
  }
  
  public function testCustomTemplate() {
    Assert::exception(function() {
      $this->generator->template = "abc.xml";
    }, \RuntimeException::class);
    $templateFilename = __DIR__ . "/template.xml";
    $this->generator->template = $templateFilename;
    Assert::same($templateFilename, $this->generator->template);
    $this->generator->dataSource = function() {
      return new Collection();
    };
    $result = $this->generator->generate();
    Assert::type("string", $result);
    $result = new \SimpleXMLElement($this->generator->generate());
    Assert::same("en", (string) $result->channel->language);
  }
}

$test = new GeneratorTest();
$test->run();
?>