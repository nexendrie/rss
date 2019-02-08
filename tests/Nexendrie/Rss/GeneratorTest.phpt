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
    $info = [
      "title" => "Test", "link" => "https://gitlab.com/nexendrie/rss/", "description" => "Test RSS Channel",
    ];
    $this->generator->dataSource = function() {
      return new Collection();
    };
    $result = $this->generator->generate($info);
    Assert::type("string", $result);
    $result = new \SimpleXMLElement($result);
    Assert::same("Test", (string) $result->channel->title);
    Assert::same("Test RSS Channel", (string) $result->channel->description);
    Assert::same(0, $this->countItems($result));
  }
  
  public function testGenerate() {
    $info = [
      "title" => "Nexendrie RSS", "link" => "https://gitlab.com/nexendrie/rss/",
      "description" => "News for package nexendrie/rss",
    ];
    $this->generator->dataSource = function() {
      $items = new Collection();
      $items[] = new RssChannelItem("Item 1", "Item 1 description", "", 123);
      return $items;
    };
    $result = $this->generator->generate($info);
    Assert::type("string", $result);
    $result = new \SimpleXMLElement($result);
    Assert::same($info["title"], (string) $result->channel->title);
    Assert::same($info["description"], (string) $result->channel->description);
    Assert::same($info["link"], (string) $result->channel->link);
    Assert::same(1, $this->countItems($result));
    Assert::type("string", (string) $result->channel->lastBuidDate);
  }
  
  public function testInvalidDataSource() {
    $this->generator->dataSource = function() {
      return [];
    };
    Assert::exception(function() {
      $this->generator->generate(["title" => "", "link" => "", "description" => "", ]);
    }, \InvalidArgumentException::class);
  }
  
  public function testShortenDescription() {
    $info = [
      "title" => "Nexendrie RSS", "link" => "https://gitlab.com/nexendrie/rss/",
      "description" => str_repeat("ABDEFGH", 20),
    ];
    $this->generator->dataSource = function() use($info) {
      $items = new Collection();
      $items[] = new RssChannelItem("Item 1", $info["description"], "", 123);
      return $items;
    };
    $this->generator->shortenDescription = 0;
    $result = new \SimpleXMLElement($this->generator->generate($info));
    Assert::same($info["description"], (string) $result->channel->item->description);
    $this->generator->shortenDescription = 10;
    $result = new \SimpleXMLElement($this->generator->generate($info));
    Assert::same(13, (strlen((string) $result->channel->item->description)));
    $this->generator->shortenDescription = 250;
    $result = new \SimpleXMLElement($this->generator->generate($info));
    Assert::same(strlen($info["description"]), (strlen((string) $result->channel->item->description)));
  }
  
  public function testCustomLastBuildDate() {
    $info = [
      "title" => "Nexendrie RSS", "link" => "https://gitlab.com/nexendrie/rss/",
      "description" => "News for package nexendrie/rss", "lastBuildDate" => function() {
        return time();
      },
    ];
    $this->generator->dataSource = function() {
      $items = new Collection();
      $items[] = new RssChannelItem("Item 1", "Item 1 description", "", 123);
      return $items;
    };
    $result = new \SimpleXMLElement($this->generator->generate($info));
    Assert::type("string", (string) $result->channel->lastBuidDate);
  }
  
  public function testInvalidLastBuildDate() {
    $this->generator->dataSource = function() {
      $items = new Collection();
      $items[] = new RssChannelItem("Item 1", "Item 1 description", "", 123);
      return $items;
    };
    Assert::exception(function() {
      $info = [
        "title" => "Nexendrie RSS", "link" => "https://gitlab.com/nexendrie/rss/",
        "description" => "News for package nexendrie/rss", "lastBuildDate" => function() {
          return "abc";
        },
      ];
      $this->generator->generate($info);
    }, \InvalidArgumentException::class, "Callback for last build date for RSS generator has to return integer.");
  }
  
  public function testResponse() {
    $info = [
      "title" => "Nexendrie RSS", "link" => "https://gitlab.com/nexendrie/rss/",
      "description" => "News for package nexendrie/rss",
    ];
    Assert::exception(function() use($info) {
      $this->generator->response($info);
    }, InvalidStateException::class);
    $this->generator->dataSource = function() {
      $items = new Collection();
      $items[] = new RssChannelItem("Item 1", "Item 1 description", "", 123);
      return $items;
    };
    $result = $this->generator->response($info);
    Assert::type(RssResponse::class, $result);
    Assert::type("string", $result->source);
  }

  public function testDateTimeFormat() {
    $info = [
      "title" => "Nexendrie RSS", "link" => "https://gitlab.com/nexendrie/rss/",
      "description" => "News for package nexendrie/rss",
    ];
    $dateTimeFormat = "Y/m/d";
    $this->generator->dateTimeFormat = $dateTimeFormat;
    Assert::same($dateTimeFormat, $this->generator->dateTimeFormat);
    $this->generator->dataSource = function() {
      return new Collection();
    };
    $result = new \SimpleXMLElement($this->generator->generate($info));
    Assert::same(date($dateTimeFormat), (string) $result->channel->lastBuildDate);
  }

  public function testPubDate() {
    $info = [
      "title" => "Nexendrie RSS", "link" => "https://gitlab.com/nexendrie/rss/",
      "description" => "News for package nexendrie/rss", "pubDate" => function() {
        return "abc";
      },
    ];
    $dateTimeFormat = "Y/m/d";
    $this->generator->dateTimeFormat = $dateTimeFormat;
    $this->generator->dataSource = function() {
      $items = new Collection();
      $items[] = new RssChannelItem("Item 1", "Item 1 description", "", 123);
      return $items;
    };
    Assert::exception(function() use($info) {
      $this->generator->generate($info);
    }, \InvalidArgumentException::class, "Callback for pub date for RSS generator has to return integer.");
    $info["pubDate"] = function() {
      return time();
    };
    $result = new \SimpleXMLElement($this->generator->generate($info));
    Assert::same(date($dateTimeFormat), (string) $result->channel->pubDate);
  }

  public function testOptionalThings() {
    $info = [
      "title" => "Nexendrie RSS", "link" => "https://gitlab.com/nexendrie/rss/", "language" => "en",
      "description" => "News for package nexendrie/rss", "copyright" => "Copyright 2019, Abc",
      "managingEditor" => "Abc", "webMaster" => "Def", "generator" => "Custom generator",
      "docs" => "https://nexendrie.gitlab.io/rss", "ttl" => 60,
      "rating" => "(PICS-1.1 \"http://www.classify.org/safesurf/\" 1 r (SS~~000 1))",
    ];
    $this->generator->dataSource = function() {
      return new Collection();
    };

    $result = new \SimpleXMLElement($this->generator->generate($info));
    Assert::same($info["language"], (string) $result->channel->language);
    Assert::same($info["copyright"], (string) $result->channel->copyright);
    Assert::same($info["managingEditor"], (string) $result->channel->managingEditor);
    Assert::same($info["webMaster"], (string) $result->channel->webMaster);
    Assert::same($info["generator"], (string) $result->channel->generator);
    Assert::same($info["docs"], (string) $result->channel->docs);
    Assert::same((string) $info["ttl"], (string) $result->channel->ttl);
    Assert::same($info["rating"], (string) $result->channel->rating);
  }

  public function testItemOptionalThings() {
    $info = [
      "title" => "Nexendrie RSS", "link" => "https://gitlab.com/nexendrie/rss/",
      "description" => "News for package nexendrie/rss",
    ];
    $this->generator->dataSource = function() {
      $collection = new Collection();
      $collection[] = $item = new RssChannelItem("Item 1", "Item 1 description", "", 123);
      $item->author = "me@mysite.com";
      $item->comments = "https://mysite.com/item/1/comments";
      $item->guid = "https://mysite.com/item/1";
      return $collection;
    };
    $result = new \SimpleXMLElement($this->generator->generate($info));
    Assert::same("me@mysite.com", (string) $result->channel->item[0]->author);
    Assert::same("https://mysite.com/item/1/comments", (string) $result->channel->item[0]->comments);
    Assert::same("https://mysite.com/item/1", (string) $result->channel->item[0]->guid);
  }

  public function testCategories() {
    $this->generator->dataSource = function() {
      return new Collection();
    };
    $info = [
      "title" => "Nexendrie RSS", "link" => "https://gitlab.com/nexendrie/rss/",
      "description" => "News for package nexendrie/rss", "categories" => [],
    ];
    $info["categories"][] = new Category("abc");
    $info["categories"][] = new Category("def", "domain");
    $result = new \SimpleXMLElement($this->generator->generate($info));
    Assert::same("abc", (string) $result->channel->category[0]);
    Assert::same("def", (string) $result->channel->category[1]);
    Assert::same("domain", (string) $result->channel->category[1]["domain"]);
    $this->generator->dataSource = function() {
      $items = new Collection();
      $items[] = $item = new RssChannelItem("Item 1", "Item 1 description", "", 123);
      $item->categories[] = new Category("abc");
      $item->categories[] = new Category("def", "domain");
      return $items;
    };
    $result = new \SimpleXMLElement($this->generator->generate($info));
    Assert::same("abc", (string) $result->channel->item->category[0]);
    Assert::same("def", (string) $result->channel->item->category[1]);
    Assert::same("domain", (string) $result->channel->item->category[1]["domain"]);
  }
  
  public function testCustomTemplate() {
    Assert::exception(function() {
      $this->generator->template = "abc.xml";
    }, \RuntimeException::class);
    $templateFilename = __DIR__ . "/template.xml";
    $this->generator->template = $templateFilename;
    Assert::same($templateFilename, $this->generator->template);
    $info = [
      "title" => "Nexendrie RSS", "link" => "https://gitlab.com/nexendrie/rss/",
      "description" => "News for package nexendrie/rss",
    ];
    $this->generator->dataSource = function() {
      return new Collection();
    };
    $result = $this->generator->generate($info);
    Assert::type("string", $result);
    $result = new \SimpleXMLElement($this->generator->generate($info));
    Assert::same("en", (string) $result->channel->language);
  }
}

$test = new GeneratorTest();
$test->run();
?>