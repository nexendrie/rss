<?php
declare(strict_types=1);

namespace Nexendrie\Rss;

use MyTester\Attributes\Group;
use MyTester\Attributes\TestSuite;
use ValueError;

#[TestSuite("Enclosure")]
#[Group("elements")]
final class EnclosureTest extends \MyTester\TestCase
{
    public function testAppendToXml(): void
    {
        $enclosure = new Enclosure("https://example.com/music/test.mp3", 15, "audio/mpeg");
        $xml = new \SimpleXMLElement("<test></test>");
        $enclosure->appendToXml($xml);
        $this->assertSame($enclosure->url, (string) $xml->enclosure["url"]);
        $this->assertSame((string) $enclosure->length, (string) $xml->enclosure["length"]);
        $this->assertSame($enclosure->type, (string) $xml->enclosure["type"]);
        $this->assertThrowsException(static function () {
            new Enclosure("test", 15, "text/plain");
        }, ValueError::class);
    }
}
