<?php
declare(strict_types=1);

namespace Nexendrie\Rss;

use Tester\Assert;
use ValueError;

require __DIR__ . "/../../bootstrap.php";

/**
 * @author Jakub Konečný
 * @testCase
 */
final class ImageTest extends \Tester\TestCase
{
    public function testUrl(): void
    {
        new Image("https://example.com/rss/image.jpeg", "title", "https://example.com");
        Assert::exception(static function () {
            new Image("test", "title", "https://example.com");
        }, ValueError::class);
    }

    public function testLink(): void
    {
        new Image("https://example.com/rss/image.jpeg", "title", "https://example.com");
        Assert::exception(static function () {
            new Image("https://example.com/rss/image.jpeg", "title", "test");
        }, ValueError::class);
    }

    public function testWidth(): void
    {
        $image = new Image("https://example.com/rss/image.jpeg", "title", "https://example.com");
        $image->width = 1;
        Assert::same(1, $image->width);
    }

    public function testHeight(): void
    {
        $image = new Image("https://example.com/rss/image.jpeg", "title", "https://example.com");
        $image->height = 1;
        Assert::same(1, $image->height);
    }

    public function testAppendToXml(): void
    {
        $image = new Image("https://example.com/rss/image.jpeg", "title", "https://example.com");
        $xml = new \SimpleXMLElement("<test></test>");
        $image->appendToXml($xml);
        Assert::same($image->url, (string) $xml->image->url);
        Assert::same($image->title, (string) $xml->image->title);
        Assert::same($image->link, (string) $xml->image->link);
        Assert::same("", (string) $xml->image->width);
        Assert::same("", (string) $xml->image->height);
        Assert::same("", (string) $xml->image->description);
        $image = new Image("https://example.com/rss/image.jpeg", "title", "https://example.com", "description");
        $image->width = 1;
        $image->height = 1;
        $xml = new \SimpleXMLElement("<test></test>");
        $image->appendToXml($xml);
        Assert::same($image->url, (string) $xml->image->url);
        Assert::same($image->title, (string) $xml->image->title);
        Assert::same($image->link, (string) $xml->image->link);
        Assert::same((string) $image->width, (string) $xml->image->width);
        Assert::same((string) $image->height, (string) $xml->image->height);
        Assert::same($image->description, (string) $xml->image->description);
    }
}

$test = new ImageTest();
$test->run();
