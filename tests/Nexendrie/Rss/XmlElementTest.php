<?php
declare(strict_types=1);

namespace Nexendrie\Rss;

use MyTester\Attributes\Group;
use MyTester\Attributes\TestSuite;
use MyTester\TestCase;

#[TestSuite("XmlElement")]
#[Group("elements")]
final class XmlElementTest extends TestCase
{
    public function testAppendToXml(): void
    {
        $element = new XmlElement("abc", "");
        $xml = new \SimpleXMLElement("<test></test>");
        $element->appendToXml($xml);
        $this->assertSame("<?xml version=\"1.0\"?>\n<test/>\n", $xml->asXML());

        $element = new XmlElement("abc", "", null, ["attr1" => "value1", "attr2" => "value2",]);
        $xml = new \SimpleXMLElement("<test></test>");
        $element->appendToXml($xml);
        $this->assertSame(
            "<?xml version=\"1.0\"?>\n<test><abc attr1=\"value1\" attr2=\"value2\"/></test>\n",
            $xml->asXML()
        );

        $element = new XmlElement("cloud", "", null, ["protocol" => CloudProtocol::HttpPost,]);
        $xml = new \SimpleXMLElement("<test></test>");
        $element->appendToXml($xml);
        $this->assertSame(
            "<?xml version=\"1.0\"?>\n<test><cloud protocol=\"http-post\"/></test>\n",
            $xml->asXML()
        );

        $element = new XmlElement("abc", "def");
        $xml = new \SimpleXMLElement("<test></test>");
        $element->appendToXml($xml);
        $this->assertSame("<?xml version=\"1.0\"?>\n<test><abc>def</abc></test>\n", $xml->asXML());

        $element = new XmlElement("abc", CloudProtocol::HttpPost);
        $xml = new \SimpleXMLElement("<test></test>");
        $element->appendToXml($xml);
        $this->assertSame(
            "<?xml version=\"1.0\"?>\n<test><abc>http-post</abc></test>\n",
            $xml->asXML()
        );

        $element = new XmlElement("abc", "def", null, [], true);
        $xml = new \SimpleXMLElement("<test></test>");
        $element->appendToXml($xml);
        $this->assertSame("<?xml version=\"1.0\"?>\n<test><abc><![CDATA[def]]></abc></test>\n", $xml->asXML());

        $element = new XmlElement("abc", "def", null, ["attr1" => "value1", "attr2" => "value2",]);
        $xml = new \SimpleXMLElement("<test></test>");
        $element->appendToXml($xml);
        $this->assertSame(
            "<?xml version=\"1.0\"?>\n<test><abc attr1=\"value1\" attr2=\"value2\">def</abc></test>\n",
            $xml->asXML()
        );

        $element = new XmlElement("abc", "def", null, ["attr1" => "value1", "attr2" => "value2",], true);
        $xml = new \SimpleXMLElement("<test></test>");
        $element->appendToXml($xml);
        $this->assertSame(
            "<?xml version=\"1.0\"?>\n<test><abc attr1=\"value1\" attr2=\"value2\"><![CDATA[def]]></abc></test>\n",
            $xml->asXML()
        );

        $element = new XmlElement("abc", ["abc", "", "def",]);
        $xml = new \SimpleXMLElement("<test></test>");
        $element->appendToXml($xml);
        $this->assertSame("<?xml version=\"1.0\"?>\n<test><abc>abc</abc><abc>def</abc></test>\n", $xml->asXML());

        $element = new XmlElement("abc", ["abc", "", "def",], null, [], true);
        $xml = new \SimpleXMLElement("<test></test>");
        $element->appendToXml($xml);
        $this->assertSame(
            "<?xml version=\"1.0\"?>\n<test><abc><![CDATA[abc]]></abc><abc><![CDATA[def]]></abc></test>\n",
            $xml->asXML()
        );

        $element = new XmlElement(
            "abc",
            null,
            null,
            [],
            false,
            [new XmlElement("def", "value1"), new XmlElement("def", "value2"),]
        );
        $xml = new \SimpleXMLElement("<test></test>");
        $element->appendToXml($xml);
        $this->assertSame(
            "<?xml version=\"1.0\"?>\n<test><abc><def>value1</def><def>value2</def></abc></test>\n",
            $xml->asXML()
        );

        $element = new XmlElement("abc", "", "https://example.com/rss/test/");
        $xml = new \SimpleXMLElement("<test></test>");
        $element->appendToXml($xml);
        $this->assertSame("<?xml version=\"1.0\"?>\n<test/>\n", $xml->asXML());

        $element = new XmlElement(
            "abc",
            "",
            "https://example.com/rss/test/",
            ["attr1" => "value1", "attr2" => "value2",]
        );
        $xml = new \SimpleXMLElement("<test xmlns:test=\"https://example.com/rss/test/\"></test>");
        $element->appendToXml($xml);
        $this->assertSame(
            "<?xml version=\"1.0\"?>
<test xmlns:test=\"https://example.com/rss/test/\"><test:abc attr1=\"value1\" attr2=\"value2\"/></test>
",
            $xml->asXML()
        );

        $element = new XmlElement("abc", "def", "https://example.com/rss/test/");
        $xml = new \SimpleXMLElement("<test xmlns:test=\"https://example.com/rss/test/\"></test>");
        $element->appendToXml($xml);
        $this->assertSame(
            "<?xml version=\"1.0\"?>
<test xmlns:test=\"https://example.com/rss/test/\"><test:abc>def</test:abc></test>
",
            $xml->asXML()
        );

        $element = new XmlElement("abc", CloudProtocol::HttpPost, "https://example.com/rss/test/");
        $xml = new \SimpleXMLElement("<test xmlns:test=\"https://example.com/rss/test/\"></test>");
        $element->appendToXml($xml);
        $this->assertSame(
            "<?xml version=\"1.0\"?>
<test xmlns:test=\"https://example.com/rss/test/\"><test:abc>http-post</test:abc></test>
",
            $xml->asXML()
        );

        $element = new XmlElement("abc", "def", "https://example.com/rss/test/", [], true);
        $xml = new \SimpleXMLElement("<test xmlns:test=\"https://example.com/rss/test/\"></test>");
        $element->appendToXml($xml);
        $this->assertSame(
            "<?xml version=\"1.0\"?>
<test xmlns:test=\"https://example.com/rss/test/\"><test:abc><![CDATA[def]]></test:abc></test>
",
            $xml->asXML()
        );

        $element = new XmlElement(
            "abc",
            "def",
            "https://example.com/rss/test/",
            ["attr1" => "value1", "attr2" => "value2",]
        );
        $xml = new \SimpleXMLElement("<test xmlns:test=\"https://example.com/rss/test/\"></test>");
        $element->appendToXml($xml);
        $this->assertSame(
            "<?xml version=\"1.0\"?>
<test xmlns:test=\"https://example.com/rss/test/\"><test:abc attr1=\"value1\" attr2=\"value2\">def</test:abc></test>
",
            $xml->asXML()
        );

        $element = new XmlElement(
            "abc",
            "def",
            "https://example.com/rss/test/",
            ["attr1" => "value1", "attr2" => "value2",],
            true
        );
        $xml = new \SimpleXMLElement("<test xmlns:test=\"https://example.com/rss/test/\"></test>");
        $element->appendToXml($xml);
        $this->assertSame(
            "<?xml version=\"1.0\"?>
<test xmlns:test=\"https://example.com/rss/test/\"><test:abc attr1=\"value1\" attr2=\"value2\"><![CDATA[def]]></test:abc></test>
",
            $xml->asXML()
        );

        $element = new XmlElement("abc", ["abc", "", "def",], "https://example.com/rss/test/");
        $xml = new \SimpleXMLElement("<test xmlns:test=\"https://example.com/rss/test/\"></test>");
        $element->appendToXml($xml);
        $this->assertSame(
            "<?xml version=\"1.0\"?>
<test xmlns:test=\"https://example.com/rss/test/\"><test:abc>abc</test:abc><test:abc>def</test:abc></test>
",
            $xml->asXML()
        );

        $element = new XmlElement("abc", ["abc", "", "def",], "https://example.com/rss/test/", [], true);
        $xml = new \SimpleXMLElement("<test xmlns:test=\"https://example.com/rss/test/\"></test>");
        $element->appendToXml($xml);
        $this->assertSame(
            "<?xml version=\"1.0\"?>
<test xmlns:test=\"https://example.com/rss/test/\"><test:abc><![CDATA[abc]]></test:abc><test:abc><![CDATA[def]]></test:abc></test>
",
            $xml->asXML()
        );

        $element = new XmlElement(
            "abc",
            null,
            "https://example.com/rss/test/",
            [],
            false,
            [new XmlElement("def", "value1"), new XmlElement("def", "value2"),]
        );
        $xml = new \SimpleXMLElement("<test xmlns:test=\"https://example.com/rss/test/\"></test>");
        $element->appendToXml($xml);
        $this->assertSame(
            "<?xml version=\"1.0\"?>
<test xmlns:test=\"https://example.com/rss/test/\"><test:abc><test:def>value1</test:def><test:def>value2</test:def></test:abc></test>
",
            $xml->asXML()
        );
    }
}
