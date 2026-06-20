<?php
declare(strict_types=1);

namespace Nexendrie\Rss\Extensions;

use DateTime;
use MyTester\Attributes\Group;
use MyTester\Attributes\TestSuite;
use Nexendrie\Rss\Collection;
use Nexendrie\Rss\Generator;
use Nexendrie\Rss\RssChannelItem;

#[TestSuite("DublinCore")]
#[Group("extensions")]
final class DublinCoreTest extends \MyTester\TestCase
{
    public function testGetName(): void
    {
        $extension = new DublinCore();
        $this->assertSame("dc", $extension->getName());
    }

    public function testExtension(): void
    {
        $generator = new Generator();
        $generator->extensions[] = $extension = new DublinCore();
        $extensionName = $extension->getName();
        $extensionNamespace = $extension->getNamespace();
        $elementName1 = DublinCore::ELEMENT_TITLE;
        $elementName2 = DublinCore::ELEMENT_CREATOR;
        $info = [
            "title" => "Nexendrie RSS", "link" => "https://gitlab.com/nexendrie/rss/",
            "description" => "News for package nexendrie/rss",
        ];
        $generator->dataSource = static function () use ($extensionName, $elementName1, $elementName2) {
            $collection = new Collection();
            $collection[] = new RssChannelItem([
                "title" => "Item 1", "description" => "Item 1 description", "link" => "https://example.com/item/1",
                "pubDate" => new DateTime(), "$extensionName:$elementName1" => "abc",
                "$extensionName:$elementName2" => "def",
            ]);
            return $collection;
        };
        $result = $generator->generate($info);
        $this->assertType("string", $result);
        $result = new \SimpleXMLElement($result);
        $namespaces = $result->getNamespaces(true);
        $this->assertSame($extension->getNamespace(), $namespaces[$extensionName]);
        $this->assertSame("abc", (string) $result->channel->item->children($extensionNamespace)->$elementName1);
        $this->assertSame("def", (string) $result->channel->item->children($extensionNamespace)->$elementName2);
    }
}
