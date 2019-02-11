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
  
  public function testToXml() {
    $item = new RssChannelItem("Item 1", "Item 1 description", "", 123);
    $item->author = "me@mysite.com";
    $item->comments = "https://mysite.com/item/1/comments";
    $item->guid = "https://mysite.com/item/1";
    $item->categories[] = new Category("abc");
    $item->enclosures[] = new Enclosure("url", 15, "text/plain");
    $xml = new \SimpleXMLElement("<test></test>");
    $item->toXml($xml, new Generator());
    Assert::same($item->author, (string) $xml->author);
    Assert::same($item->comments, (string) $xml->comments);
    Assert::same($item->guid, (string) $xml->guid);
    Assert::same($item->categories[0]->domain, (string) $xml->categories->category);
    Assert::same($item->enclosures[0]->url, (string) $xml->enclosure["url"]);
    Assert::same((string) $item->enclosures[0]->length, (string) $xml->enclosure["length"]);
    Assert::same($item->enclosures[0]->type, (string) $xml->enclosure["type"]);
  }

  public function testShortenDescription() {
    $generator = new Generator();
    $description = str_repeat("ABDEFGH", 20);
    $item = new RssChannelItem("Item 1", $description, "", 123);

    $xml = new \SimpleXMLElement("<test></test>");
    $generator->shortenDescription = 0;
    $item->toXml($xml, $generator);
    Assert::same($description, (string) $xml->description);

    $xml = new \SimpleXMLElement("<test></test>");
    $generator->shortenDescription = 10;
    $item->toXml($xml, $generator);
    Assert::same(13, strlen((string) $xml->description));

    $xml = new \SimpleXMLElement("<test></test>");
    $generator->shortenDescription = 150;
    $item->toXml($xml, $generator);
    Assert::same($description, (string) $xml->description);
  }
}

$test = new RssChannelItemTest();
$test->run();
?>