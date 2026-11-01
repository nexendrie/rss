<?php
declare(strict_types=1);

namespace Nexendrie\Rss;

use MyTester\Attributes\Group;
use MyTester\Attributes\TestSuite;
use MyTester\TestCase;
use SimpleXMLElement;

#[TestSuite("CDataElement")]
#[Group("elements")]
final class CDataElementTest extends TestCase
{
    public function testAppendToXml(): void
    {
        $element = new CDataElement("abc", "");
        $xml = new SimpleXMLElement("<test></test>");
        $element->appendToXml($xml);
        $this->assertSame("", (string) $xml->abc);
        $this->assertSame("<?xml version=\"1.0\"?>
<test/>
", (string) $xml->asXML());

        $element = new CDataElement("abc", "def");
        $xml = new SimpleXMLElement("<test></test>");
        $element->appendToXml($xml);
        $this->assertSame("def", (string) $xml->abc);
        $this->assertSame("<?xml version=\"1.0\"?>
<test><abc><![CDATA[def]]></abc></test>
", (string) $xml->asXML());

        $element = new CDataElement("abc", "čř<test");
        $xml = new SimpleXMLElement("<test></test>");
        $element->appendToXml($xml);
        $this->assertSame("čř<test", (string) $xml->abc);
        $this->assertSame("<?xml version=\"1.0\"?>
<test><abc><![CDATA[čř<test]]></abc></test>
", (string) $xml->asXML());

        $element = new CDataElement("abc", "def", "https://example.com/ns/");
        $xml = new SimpleXMLElement("<test xmlns:ns=\"https://example.com/ns/\"></test>");
        $element->appendToXml($xml);
        $this->assertSame("def", (string) $xml->children("https://example.com/ns/")->abc);
        $this->assertSame("<?xml version=\"1.0\"?>
<test xmlns:ns=\"https://example.com/ns/\"><ns:abc><![CDATA[def]]></ns:abc></test>
", (string) $xml->asXML());

        $element = new CDataElement("abc", "čř<test", "https://example.com/ns/");
        $xml = new SimpleXMLElement("<test xmlns:ns=\"https://example.com/ns/\"></test>");
        $element->appendToXml($xml);
        $this->assertSame("čř<test", (string) $xml->children("https://example.com/ns/")->abc);
        $this->assertSame("<?xml version=\"1.0\"?>
<test xmlns:ns=\"https://example.com/ns/\"><ns:abc><![CDATA[čř<test]]></ns:abc></test>
", (string) $xml->asXML());
    }
}
