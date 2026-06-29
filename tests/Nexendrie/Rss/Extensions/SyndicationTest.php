<?php
declare(strict_types=1);

namespace Nexendrie\Rss\Extensions;

use DateTime;
use MyTester\Attributes\Group;
use MyTester\Attributes\TestSuite;
use Nexendrie\Rss\Collection;
use Nexendrie\Rss\Extensions\Syndication\UpdatePeriod;
use Nexendrie\Rss\Generator;
use Nexendrie\Rss\RssChannelItem;

#[TestSuite("Syndication")]
#[Group("extensions")]
final class SyndicationTest extends \MyTester\TestCase
{
    public function testGetName(): void
    {
        $extension = new Syndication();
        $this->assertSame("sy", $extension->getName());
    }

    public function testExtension(): void
    {
        $generator = new Generator();
        $generator->extensions[] = $extension = new Syndication();
        $extensionName = $extension->getName();
        $extensionNamespace = $extension->getNamespace();
        $elementName1 = Syndication::ELEMENT_UPDATE_PERIOD;
        $elementName2 = Syndication::ELEMENT_UPDATE_FREQUENCY;
        $elementName3 = Syndication::ELEMENT_UPDATE_BASE;
        $info = [
            "title" => "Nexendrie RSS", "link" => "https://gitlab.com/nexendrie/rss/",
            "description" => "News for package nexendrie/rss", "$extensionName:$elementName1" => UpdatePeriod::Hourly,
            "$extensionName:$elementName2" => 1, "$extensionName:$elementName3" => "2026-06-30",
        ];
        $generator->dataSource = static function () {
            $collection = new Collection();
            $collection[] = new RssChannelItem([
                "title" => "Item 1", "description" => "Item 1 description", "link" => "https://example.com/item/1",
                "pubDate" => new DateTime(),
            ]);
            return $collection;
        };
        $result = $generator->generate($info);
        $this->assertType("string", $result);
        $result = new \SimpleXMLElement($result);
        $namespaces = $result->getNamespaces(true);
        $this->assertSame($extension->getNamespace(), $namespaces[$extensionName]);
        $this->assertSame(
            UpdatePeriod::Hourly->value,
            (string) $result->channel->children($extensionNamespace)->$elementName1
        );
        $this->assertSame("1", (string) $result->channel->children($extensionNamespace)->$elementName2);
        $this->assertSame("2026-06-30", (string) $result->channel->children($extensionNamespace)->$elementName3);
    }
}
