<?php
declare(strict_types=1);

namespace Nexendrie\Rss;

use Tester\Assert;

require __DIR__ . "/../../bootstrap.php";

class RssChannelItemTest extends \Tester\TestCase {
  /** @var RssChannelItem */
  protected $item;
  
  function setUp() {
    $this->item = new RssChannelItem("title", "description", "link", "pubDate");
  }
  
  function testTitle() {
    $this->item->title = "abc";
    Assert::same("abc", $this->item->title);
  }
  
  function testDescription() {
    $this->item->description = "abc";
    Assert::same("abc", $this->item->description);
  }
  
  function testLink() {
    $this->item->link = "abc";
    Assert::same("abc", $this->item->link);
  }
  
  function testPubDate() {
    $this->item->pubDate = "abc";
    Assert::same("abc", $this->item->pubDate);
  }
}

$test = new RssChannelItemTest;
$test->run();
?>