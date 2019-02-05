<?php
declare(strict_types=1);

namespace Nexendrie\Rss;

use Tester\Assert;

require __DIR__ . "/../../bootstrap.php";

final class RssChannelItemTest extends \Tester\TestCase {
  /** @var RssChannelItem */
  protected $item;
  
  public function setUp() {
    $this->item = new RssChannelItem("title", "description", "link", 1);
  }
  
  public function testTitle() {
    $this->item->title = "abc";
    Assert::same("abc", $this->item->title);
  }
  
  public function testDescription() {
    $this->item->description = "abc";
    Assert::same("abc", $this->item->description);
  }
  
  public function testLink() {
    $this->item->link = "abc";
    Assert::same("abc", $this->item->link);
  }
  
  public function testPubDate() {
    $this->item->pubDate = 123;
    Assert::same(123, $this->item->pubDate);
  }

  public function testAuthor() {
    $this->item->author = "me@mysite.com";
    Assert::same("me@mysite.com", $this->item->author);
  }

  public function testComments() {
    $this->item->comments = "https://mysite.com/item/1/comments";
    Assert::same("https://mysite.com/item/1/comments", $this->item->comments);
  }

  public function testGuid() {
    $this->item->guid = "https://mysite.com/item/1";
    Assert::same("https://mysite.com/item/1", $this->item->guid);
  }
}

$test = new RssChannelItemTest();
$test->run();
?>