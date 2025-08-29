<?php
declare(strict_types=1);

namespace Nexendrie\Rss;

use Tester\Assert;

require __DIR__ . "/../../bootstrap.php";

/**
 * @author Jakub Konečný
 * @testCase
 */
final class RssChannelItemTest extends \Tester\TestCase
{
    public function testToXml(): void
    {
        $data = [
            "title" => "Item 1", "description" => "Item 1 description", "link" => "", "pubDate" => 123,
            "author" => "me@mysite.com", "comments" => "https://mysite.com/item/1/comments",
            "guid" => "https://mysite.com/item/1",
        ];
        $category = new Category("abc");
        $data["categories"] = CategoriesCollection::fromArray([$category,]);
        $enclosure = new Enclosure("url", 15, "text/plain");
        $data["enclosures"] = EnclosuresCollection::fromArray([$enclosure,]);
        $item = new RssChannelItem($data);
        $xml = new \SimpleXMLElement("<test></test>");
        $item->toXml($xml, new Generator());
        Assert::same($data["author"], (string) $xml->author);
        Assert::same($data["comments"], (string) $xml->comments);
        Assert::same($data["guid"], (string) $xml->guid);
        Assert::same($category->domain, (string) $xml->categories->category);
        Assert::same($enclosure->url, (string) $xml->enclosure["url"]);
        Assert::same((string) $enclosure->length, (string) $xml->enclosure["length"]);
        Assert::same($enclosure->type, (string) $xml->enclosure["type"]);
    }

    public function testShortenDescription(): void
    {
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
