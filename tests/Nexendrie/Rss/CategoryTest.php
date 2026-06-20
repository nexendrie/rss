<?php
declare(strict_types=1);

namespace Nexendrie\Rss;

use MyTester\Attributes\Group;
use MyTester\Attributes\TestSuite;

#[TestSuite("Category")]
#[Group("elements")]
final class CategoryTest extends \MyTester\TestCase
{
    public function testAppendToXml(): void
    {
        $category = new Category("abc");
        $xml = new \SimpleXMLElement("<test></test>");
        $category->appendToXml($xml);
        $this->assertSame($category->identifier, (string) $xml->category);
        $this->assertSame("", (string) $xml->category["domain"]);
        $category = new Category("def", "domain");
        $xml = new \SimpleXMLElement("<test></test>");
        $category->appendToXml($xml);
        $this->assertSame($category->identifier, (string) $xml->category);
        $this->assertSame($category->domain, (string) $xml->category["domain"]);
    }
}
