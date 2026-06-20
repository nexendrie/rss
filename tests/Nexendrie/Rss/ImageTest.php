<?php
declare(strict_types=1);

namespace Nexendrie\Rss;

use MyTester\Attributes\Group;
use MyTester\Attributes\TestSuite;
use ValueError;

#[TestSuite("Image")]
#[Group("elements")]
final class ImageTest extends \MyTester\TestCase
{
    public function testUrl(): void
    {
        new Image("https://example.com/rss/image.jpeg", "title", "https://example.com");
        $this->assertThrowsException(static function () {
            new Image("test", "title", "https://example.com");
        }, ValueError::class);
    }

    public function testLink(): void
    {
        new Image("https://example.com/rss/image.jpeg", "title", "https://example.com");
        $this->assertThrowsException(static function () {
            new Image("https://example.com/rss/image.jpeg", "title", "test");
        }, ValueError::class);
    }

    public function testWidth(): void
    {
        $image = new Image("https://example.com/rss/image.jpeg", "title", "https://example.com");
        $image->width = 1;
        $this->assertSame(1, $image->width);
    }

    public function testHeight(): void
    {
        $image = new Image("https://example.com/rss/image.jpeg", "title", "https://example.com");
        $image->height = 1;
        $this->assertSame(1, $image->height);
    }

    public function testAppendToXml(): void
    {
        $image = new Image("https://example.com/rss/image.jpeg", "title", "https://example.com");
        $xml = new \SimpleXMLElement("<test></test>");
        $image->appendToXml($xml);
        $this->assertSame($image->url, (string) $xml->image->url);
        $this->assertSame($image->title, (string) $xml->image->title);
        $this->assertSame($image->link, (string) $xml->image->link);
        $this->assertSame("", (string) $xml->image->width);
        $this->assertSame("", (string) $xml->image->height);
        $this->assertSame("", (string) $xml->image->description);
        $image = new Image("https://example.com/rss/image.jpeg", "title", "https://example.com", "description");
        $image->width = 1;
        $image->height = 1;
        $xml = new \SimpleXMLElement("<test></test>");
        $image->appendToXml($xml);
        $this->assertSame($image->url, (string) $xml->image->url);
        $this->assertSame($image->title, (string) $xml->image->title);
        $this->assertSame($image->link, (string) $xml->image->link);
        $this->assertSame((string) $image->width, (string) $xml->image->width);
        $this->assertSame((string) $image->height, (string) $xml->image->height);
        $this->assertSame($image->description, (string) $xml->image->description);
    }
}
