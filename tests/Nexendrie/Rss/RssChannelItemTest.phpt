<?php
declare(strict_types=1);

namespace Nexendrie\Rss;

use Tester\Assert;

require __DIR__ . "/../../bootstrap.php";

/**
 * @author Jakub Konečný
 * @testCase
 */
final class RssChannelItemTest extends \Tester\TestCase {
  protected RssChannelItem $item;
  
  public function setUp() {
    $this->item = new RssChannelItem(["title" => "title", "description" => "description", "link" => "link", "pubDate" => 1]);
  }
  
  public function testToXml() {
    $data = [
      "title" => "Item 1", "description" => "Item 1 description", "link" => "", "pubDate" => 123, "author" => "me@mysite.com",
      "comments" => "https://mysite.com/item/1/comments", "guid" => "https://mysite.com/item/1",
    ];
    $data["categories"] = CategoriesCollection::fromArray([new Category("abc"),]);
    $data["enclosures"] = EnclosuresCollection::fromArray([new Enclosure("url", 15, "text/plain"),]);
    $item = new RssChannelItem($data);
    $xml = new \SimpleXMLElement("<test></test>");
    $item->toXml($xml, new Generator());
    Assert::same($data["author"], (string) $xml->author);
    Assert::same($data["comments"], (string) $xml->comments);
    Assert::same($data["guid"], (string) $xml->guid);
    Assert::same($data["categories"][0]->domain, (string) $xml->categories->category);
    Assert::same($data["enclosures"][0]->url, (string) $xml->enclosure["url"]);
    Assert::same((string) $data["enclosures"][0]->length, (string) $xml->enclosure["length"]);
    Assert::same($data["enclosures"][0]->type, (string) $xml->enclosure["type"]);
  }

  public function testShortenDescription() {
    $generator = new Generator();
    $description = str_repeat("ABDEFGH", 20);
    $item = new RssChannelItem(["title" => "Item 1", "description" => $description, "link" => "", "pubDate" => 123,]);

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