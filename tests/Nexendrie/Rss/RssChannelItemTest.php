<?php
declare(strict_types=1);

namespace Nexendrie\Rss;

use DateTime;
use MyTester\Attributes\TestSuite;

#[TestSuite("RssChannelItem")]
final class RssChannelItemTest extends \MyTester\TestCase
{
    public function testToXml(): void
    {
        $data = [
            "title" => "Item 1", "description" => "Item 1 description", "link" => "https://example.com/item/1",
            "pubDate" => new DateTime(), "author" => "me@mysite.com",
            "comments" => "https://mysite.com/item/1/comments", "guid" => "https://mysite.com/item/1",
        ];
        $category = new Category("abc");
        $data["categories"] = CategoriesCollection::fromArray([$category,]);
        $enclosure = new Enclosure("https://example.com/music/test.mp3", 15, "audio/mpeg");
        $data["enclosure"] = $enclosure;
        $item = new RssChannelItem($data);
        $xml = new \SimpleXMLElement("<test></test>");
        $item->toXml($xml, new Generator());
        $this->assertSame($data["author"], (string) $xml->author);
        $this->assertSame($data["comments"], (string) $xml->comments);
        $this->assertSame($data["guid"], (string) $xml->guid);
        $this->assertSame($category->domain, (string) $xml->categories->category);
        $this->assertSame($enclosure->url, (string) $xml->enclosure["url"]);
        $this->assertSame((string) $enclosure->length, (string) $xml->enclosure["length"]);
        $this->assertSame($enclosure->type, (string) $xml->enclosure["type"]);
    }

    public function testShortenDescription(): void
    {
        $generator = new Generator();
        $description = str_repeat("ABDEFGH", 20);
        $item = new RssChannelItem([
            "title" => "Item 1", "description" => $description, "link" => "https://example.com/item/1",
            "pubDate" => new DateTime(),
        ]);

        $xml = new \SimpleXMLElement("<test></test>");
        $generator->shortenDescription = 0;
        $item->toXml($xml, $generator);
        $this->assertSame($description, (string) $xml->description);

        $xml = new \SimpleXMLElement("<test></test>");
        $generator->shortenDescription = 10;
        $item->toXml($xml, $generator);
        $this->assertSame(13, strlen((string) $xml->description));

        $xml = new \SimpleXMLElement("<test></test>");
        $generator->shortenDescription = 150;
        $item->toXml($xml, $generator);
        $this->assertSame($description, (string) $xml->description);
    }
}
