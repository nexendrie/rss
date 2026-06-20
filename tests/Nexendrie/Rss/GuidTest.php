<?php
declare(strict_types=1);

namespace Nexendrie\Rss;

use MyTester\Attributes\Group;
use MyTester\Attributes\TestSuite;
use SimpleXMLElement;

#[TestSuite("Guid")]
#[Group("elements")]
final class GuidTest extends \MyTester\TestCase
{
    public function testAppendToXml(): void
    {
        $guid = new Guid("abc");
        $xml = new SimpleXMLElement("<test></test>");
        $guid->appendToXml($xml);
        $this->assertSame($guid->value, (string) $xml->guid);
        $this->assertSame("", (string) $xml->guid["isPermaLink"]);

        $guid = new Guid("abc", true);
        $xml = new SimpleXMLElement("<test></test>");
        $guid->appendToXml($xml);
        $this->assertSame($guid->value, (string) $xml->guid);
        $this->assertSame("true", (string) $xml->guid["isPermaLink"]);

        $guid = new Guid("abc", false);
        $xml = new SimpleXMLElement("<test></test>");
        $guid->appendToXml($xml);
        $this->assertSame($guid->value, (string) $xml->guid);
        $this->assertSame("false", (string) $xml->guid["isPermaLink"]);
    }
}
