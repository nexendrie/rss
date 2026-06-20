<?php
declare(strict_types=1);

namespace Nexendrie\Rss\Extensions;

use DateTime;
use MyTester\Attributes\Group;
use MyTester\Attributes\TestSuite;
use Nexendrie\Rss\Collection;
use Nexendrie\Rss\Generator;
use Nexendrie\Rss\RssChannelItem;

#[TestSuite("CreativeCommons")]
#[Group("extensions")]
final class CreativeCommonsTest extends \MyTester\TestCase
{
    public function testGetName(): void
    {
        $extension = new CreativeCommons();
        $this->assertSame("creativeCommons", $extension->getName());
    }

    public function testExtension(): void
    {
        $generator = new Generator();
        $generator->extensions[] = $extension = new CreativeCommons();
        $extensionName = $extension->getName();
        $extensionNamespace = $extension->getNamespace();
        $elementName = CreativeCommons::ELEMENT_LICENSE;
        $info = [
            "title" => "Nexendrie RSS", "link" => "https://gitlab.com/nexendrie/rss/",
            "description" => "News for package nexendrie/rss",
        ];
        $generator->dataSource = static function () use ($extensionName, $elementName) {
            $collection = new Collection();
            $collection[] = new RssChannelItem([
                "title" => "Item 1", "description" => "Item 1 description", "link" => "https://example.com/item/1",
                "pubDate" => new DateTime(),
                "$extensionName:$elementName" => [
                    "https://creativecommons.org/licenses/by-nc-sa/2.5/",
                    "https://creativecommons.org/licenses/by-nc-sa/4.0/",
                ],
            ]);
            return $collection;
        };
        $result = $generator->generate($info);
        $this->assertType("string", $result);
        $result = new \SimpleXMLElement($result);
        $namespaces = $result->getNamespaces(true);
        $this->assertSame($extension->getNamespace(), $namespaces[$extensionName]);
        $this->assertCount(2, $result->channel->item->children($extensionNamespace)->$elementName);
        $this->assertSame(
            "https://creativecommons.org/licenses/by-nc-sa/2.5/",
            (string) $result->channel->item->children($extensionNamespace)->$elementName[0]
        );
        $this->assertSame(
            "https://creativecommons.org/licenses/by-nc-sa/4.0/",
            (string) $result->channel->item->children($extensionNamespace)->$elementName[1]
        );
    }
}
