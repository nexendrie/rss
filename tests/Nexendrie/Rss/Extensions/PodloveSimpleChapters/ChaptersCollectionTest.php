<?php
declare(strict_types=1);

namespace Nexendrie\Rss\Extensions\PodloveSimpleChapters;

use MyTester\Attributes\Group;
use MyTester\Attributes\TestSuite;
use MyTester\TestCase;
use Nexendrie\Rss\Extensions\PodloveSimpleChapters;

#[TestSuite("ChaptersCollection")]
#[Group("extensions")]
#[Group("podloveSimpleChapters")]
final class ChaptersCollectionTest extends TestCase
{
    public function testAppendToXml(): void
    {
        $collection = new ChaptersCollection();
        $collection[] = new Chapter(1, "abc");
        $collection[] = new Chapter("11:52", "def", "https://example.com", "https://example.com/rss/image.jpeg");
        $xml = new \SimpleXMLElement("<test></test>");
        $collection->appendToXml($xml);
        $chapter1 = $xml->children(PodloveSimpleChapters::NAMESPACE)->chapters->chapter[0];
        $this->assertSame("1", (string) $chapter1->attributes()["start"]);
        $this->assertSame("abc", (string) $chapter1->attributes()["title"]);
        $this->assertSame("", (string) $chapter1->attributes()["href"]);
        $this->assertSame("", (string) $chapter1->attributes()["image"]);
        $chapter1 = $xml->children(PodloveSimpleChapters::NAMESPACE)->chapters->chapter[1];
        $this->assertSame("11:52", (string) $chapter1->attributes()["start"]);
        $this->assertSame("def", (string) $chapter1->attributes()["title"]);
        $this->assertSame("https://example.com", (string) $chapter1->attributes()["href"]);
        $this->assertSame("https://example.com/rss/image.jpeg", (string) $chapter1->attributes()["image"]);
    }
}
