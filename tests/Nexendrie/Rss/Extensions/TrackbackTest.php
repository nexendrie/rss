<?php
declare(strict_types=1);

namespace Nexendrie\Rss\Extensions;

use DateTime;
use MyTester\Attributes\Group;
use MyTester\Attributes\TestSuite;
use Nexendrie\Rss\Collection;
use Nexendrie\Rss\Generator;
use Nexendrie\Rss\RssChannelItem;

#[TestSuite("Trackback")]
#[Group("extensions")]
final class TrackbackTest extends \MyTester\TestCase
{
    public function testGetName(): void
    {
        $extension = new Trackback();
        $this->assertSame("trackback", $extension->getName());
    }

    public function testExtension(): void
    {
        $generator = new Generator();
        $generator->extensions[] = $extension = new Trackback();
        $extensionName = $extension->getName();
        $extensionNamespace = $extension->getNamespace();
        $elementName1 = Trackback::ELEMENT_ABOUT;
        $elementName2 = Trackback::ELEMENT_PING;
        $info = [
            "title" => "Nexendrie RSS", "link" => "https://gitlab.com/nexendrie/rss/",
            "description" => "News for package nexendrie/rss",
        ];
        $generator->dataSource = static function () use ($extensionName, $elementName1, $elementName2) {
            $collection = new Collection();
            $collection[] = new RssChannelItem([
                "title" => "Item 1", "description" => "Item 1 description", "link" => "https://example.com/item/1",
                "pubDate" => new DateTime(),
                "$extensionName:$elementName1" => ["https://test.example.com/trackback?item=ex1",],
                "$extensionName:$elementName2" => "https://example.com/trackback?item=1",
            ]);
            return $collection;
        };
        $result = $generator->generate($info);
        $this->assertType("string", $result);
        $result = new \SimpleXMLElement($result);
        $namespaces = $result->getNamespaces(true);
        $this->assertSame($extension->getNamespace(), $namespaces[$extensionName]);
        $this->assertSame(
            "https://test.example.com/trackback?item=ex1",
            (string) $result->channel->item->children($extensionNamespace)->$elementName1
        );
        $this->assertSame(
            "https://example.com/trackback?item=1",
            (string) $result->channel->item->children($extensionNamespace)->$elementName2
        );
    }
}
