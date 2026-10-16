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

#[TestSuite("W3CBasicGeo")]
#[Group("extensions")]
final class W3CBasicGeoTest extends \MyTester\TestCase
{
    public function testGetName(): void
    {
        $extension = new W3CBasicGeo();
        $this->assertSame("geo", $extension->getName());
    }

    public function testExtension(): void
    {
        $generator = new Generator();
        $generator->extensions[] = $extension = new W3CBasicGeo();
        $extensionName = $extension->getName();
        $extensionNamespace = $extension->getNamespace();
        $elementName1 = W3CBasicGeo::ELEMENT_LATITUDE;
        $elementName2 = W3CBasicGeo::ELEMENT_LONGITUDE;
        $elementName3 = W3CBasicGeo::ELEMENT_ALTITUDE;
        $info = [
            "title" => "Nexendrie RSS", "link" => "https://gitlab.com/nexendrie/rss/",
            "description" => "News for package nexendrie/rss", "$extensionName:$elementName1" => 11.5,
            "$extensionName:$elementName2" => 10.0,
        ];
        $generator->dataSource = static function () use ($extensionName, $elementName3) {
            $collection = new Collection();
            $collection[] = new RssChannelItem([
                "title" => "Item 1", "description" => "Item 1 description", "link" => "https://example.com/item/1",
                "pubDate" => new DateTime(), "$extensionName:$elementName3" => 15.801,
            ]);
            return $collection;
        };
        $result = $generator->generate($info);
        $this->assertType("string", $result);
        $result = new \SimpleXMLElement($result);
        $namespaces = $result->getNamespaces(true);
        $this->assertSame($extension->getNamespace(), $namespaces[$extensionName]);
        $this->assertSame(
            "11.5",
            (string) $result->channel->children($extensionNamespace)->$elementName1
        );
        $this->assertSame("10", (string) $result->channel->children($extensionNamespace)->$elementName2);
        $this->assertSame("15.801", (string) $result->channel->item->children($extensionNamespace)->$elementName3);
    }
}
