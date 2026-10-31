<?php
declare(strict_types=1);

namespace Nexendrie\Rss\Extensions;

use DateTime;
use MyTester\Attributes\Group;
use MyTester\Attributes\TestSuite;
use MyTester\TestCase;
use Nexendrie\Rss\Collection;
use Nexendrie\Rss\Extensions\PodloveSimpleChapters\Chapter;
use Nexendrie\Rss\Extensions\PodloveSimpleChapters\ChaptersCollection;
use Nexendrie\Rss\Generator;
use Nexendrie\Rss\RssChannelItem;

#[TestSuite("PodloveSimpleChapters")]
#[Group("extensions")]
#[Group("podloveSimpleChapters")]
final class PodloveSimpleChaptersTest extends TestCase
{
    public function testGetName(): void
    {
        $extension = new PodloveSimpleChapters();
        $this->assertSame("psc", $extension->getName());
    }

    public function testExtension(): void
    {
        $generator = new Generator();
        $generator->extensions[] = $extension = new PodloveSimpleChapters();
        $extensionName = $extension->getName();
        $extensionNamespace = $extension->getNamespace();
        $elementName1 = PodloveSimpleChapters::ELEMENT_CHAPTERS;
        $info = [
            "title" => "Nexendrie RSS", "link" => "https://gitlab.com/nexendrie/rss/",
            "description" => "News for package nexendrie/rss",
        ];
        $generator->dataSource = static function () use ($extensionName, $elementName1) {
            $collection = new Collection();
            $chapters = new ChaptersCollection();
            $chapters[] = new Chapter(0, "abc");
            $collection[] = new RssChannelItem([
                "title" => "Item 1", "description" => "Item 1 description", "link" => "https://example.com/item/1",
                "pubDate" => new DateTime(), "$extensionName:$elementName1" => $chapters,
            ]);
            return $collection;
        };
        $result = $generator->generate($info);
        $this->assertType("string", $result);
        $result = new \SimpleXMLElement($result);
        $namespaces = $result->getNamespaces(true);
        $this->assertSame($extension->getNamespace(), $namespaces[$extensionName]);
        $element1 = $result->channel->item->children($extensionNamespace)->$elementName1;
        $this->assertSame("0", (string) $element1->chapter->attributes()["start"]);
        $this->assertSame("abc", (string) $element1->chapter->attributes()["title"]);
    }
}
