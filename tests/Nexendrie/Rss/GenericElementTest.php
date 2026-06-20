<?php
declare(strict_types=1);

namespace Nexendrie\Rss;

use MyTester\Attributes\Group;
use MyTester\Attributes\TestSuite;

#[TestSuite("GenericElement")]
#[Group("elements")]
final class GenericElementTest extends \MyTester\TestCase
{
    public function testAppendToXml(): void
    {
        $element = new GenericElement("abc", "");
        $xml = new \SimpleXMLElement("<test></test>");
        $element->appendToXml($xml);
        $this->assertSame("", (string) $xml->abc);

        $element->value = "def";
        $element->appendToXml($xml);
        $this->assertSame("def", (string) $xml->abc);

        $element = new GenericElement("abc", ["abc", "", "def",]);
        $xml = new \SimpleXMLElement("<test></test>");
        $element->appendToXml($xml);
        $this->assertSame("abc", (string) $xml->abc[0]);
        $this->assertSame("def", (string) $xml->abc[1]);
    }
}
