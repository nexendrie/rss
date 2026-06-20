<?php
declare(strict_types=1);

namespace Nexendrie\Rss;

use MyTester\Attributes\Group;
use MyTester\Attributes\TestSuite;
use ValueError;

#[TestSuite("Source")]
#[Group("elements")]
final class SourceTest extends \MyTester\TestCase
{
    public function testAppendToXml(): void
    {
        $source = new Source("", "title");
        $xml = new \SimpleXMLElement("<test></test>");
        $source->appendToXml($xml);
        $this->assertSame("", (string) $xml->source);
        $this->assertSame("", (string) $xml->source["url"]);
        $source = new Source("https://test.example.com/item/1", "");
        $xml = new \SimpleXMLElement("<test></test>");
        $source->appendToXml($xml);
        $this->assertSame("", (string) $xml->source);
        $this->assertSame($source->url, (string) $xml->source["url"]);
        $source = new Source("https://test.example.com/item/1", "title");
        $xml = new \SimpleXMLElement("<test></test>");
        $source->appendToXml($xml);
        $this->assertSame($source->title, (string) $xml->source);
        $this->assertSame($source->url, (string) $xml->source["url"]);
        $this->assertThrowsException(static function () {
            new Source("test");
        }, ValueError::class);
    }
}
