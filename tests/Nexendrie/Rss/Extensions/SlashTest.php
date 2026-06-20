<?php
declare(strict_types=1);

namespace Nexendrie\Rss\Extensions;

use DateTime;
use MyTester\Attributes\Group;
use MyTester\Attributes\TestSuite;
use Nexendrie\Rss\Collection;
use Nexendrie\Rss\Generator;
use Nexendrie\Rss\RssChannelItem;

#[TestSuite("Slash")]
#[Group("extensions")]
final class SlashTest extends \MyTester\TestCase
{
    public function testGetName(): void
    {
        $extension = new Slash();
        $this->assertSame("slash", $extension->getName());
    }

    public function testExtension(): void
    {
        $generator = new Generator();
        $generator->extensions[] = $extension = new Slash();
        $extensionName = $extension->getName();
        $extensionNamespace = $extension->getNamespace();
        $elementName1 = Slash::ELEMENT_SECTION;
        $elementName2 = Slash::ELEMENT_DEPARTMENT;
        $elementName3 = Slash::ELEMENT_COMMENTS;
        $elementName4 = Slash::ELEMENT_HIT_PARADE;
        $info = [
            "title" => "Nexendrie RSS", "link" => "https://gitlab.com/nexendrie/rss/",
            "description" => "News for package nexendrie/rss",
        ];
        $generator->dataSource = static function () use ($extensionName, $elementName1, $elementName2, $elementName3, $elementName4) {
            $collection = new Collection();
            $collection[] = new RssChannelItem([
                "title" => "Item 1", "description" => "Item 1 description", "link" => "https://example.com/item/1",
                "pubDate" => new DateTime(), "$extensionName:$elementName1" => "abc",
                "$extensionName:$elementName2" => "def", "$extensionName:$elementName3" => 1,
                "$extensionName:$elementName4" => "1,2,3",
            ]);
            return $collection;
        };
        $result = $generator->generate($info);
        $this->assertType("string", $result);
        $result = new \SimpleXMLElement($result);
        $namespaces = $result->getNamespaces(true);
        $this->assertSame($extension->getNamespace(), $namespaces[$extensionName]);
        $this->assertSame("abc", (string) $result->channel->item->children($extensionNamespace, false)->$elementName1);
        $this->assertSame("def", (string) $result->channel->item->children($extensionNamespace, false)->$elementName2);
        $this->assertSame("1", (string) $result->channel->item->children($extensionNamespace, false)->$elementName3);
        $this->assertSame("1,2,3", (string) $result->channel->item->children($extensionNamespace, false)->$elementName4);
    }
}
