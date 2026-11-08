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
        $element = new GenericElement("abc", ""); // @phpstan-ignore method.deprecatedClass, new.deprecatedClass
        $xml = new \SimpleXMLElement("<test></test>");
        $element->appendToXml($xml); // @phpstan-ignore method.deprecatedClass
        $this->assertSame("", (string) $xml->abc);

        $element->value = "def"; // @phpstan-ignore property.deprecatedClass
        $element->appendToXml($xml); // @phpstan-ignore method.deprecatedClass
        $this->assertSame("def", (string) $xml->abc);

        // @phpstan-ignoremethod.deprecatedClass, new.deprecatedClass
        $element = new GenericElement("abc", ["abc", "", "def",]);
        $xml = new \SimpleXMLElement("<test></test>");
        $element->appendToXml($xml); // @phpstan-ignore method.deprecatedClass
        $this->assertSame("abc", (string) $xml->abc[0]);
        $this->assertSame("def", (string) $xml->abc[1]);
    }
}
