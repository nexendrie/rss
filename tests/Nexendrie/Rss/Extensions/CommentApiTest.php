<?php
declare(strict_types=1);

namespace Nexendrie\Rss\Extensions;

use DateTime;
use MyTester\Attributes\Group;
use MyTester\Attributes\TestSuite;
use Nexendrie\Rss\Collection;
use Nexendrie\Rss\Generator;
use Nexendrie\Rss\RssChannelItem;

#[TestSuite("CommentApi")]
#[Group("extensions")]
final class CommentApiTest extends \MyTester\TestCase
{
    public function testGetName(): void
    {
        $extension = new CommentApi();
        $this->assertSame("wfw", $extension->getName());
    }

    public function testExtension(): void
    {
        $generator = new Generator();
        $generator->extensions[] = $extension = new CommentApi();
        $extensionName = $extension->getName();
        $extensionNamespace = $extension->getNamespace();
        $elementName1 = CommentApi::ELEMENT_COMMENT;
        $elementName2 = CommentApi::ELEMENT_COMMENT_RSS;
        $info = [
            "title" => "Nexendrie RSS", "link" => "https://gitlab.com/nexendrie/rss/",
            "description" => "News for package nexendrie/rss",
        ];
        $generator->dataSource = static function () use ($extensionName, $elementName1, $elementName2) {
            $collection = new Collection();
            $collection[] = new RssChannelItem([
                "title" => "Item 1", "description" => "Item 1 description", "link" => "https://example.com/item/1",
                "pubDate" => new DateTime(), "$extensionName:$elementName1" => "https://example.com/comment?item=1",
                "$extensionName:$elementName2" => "https://example.com/rss/item/1",
            ]);
            return $collection;
        };
        $result = $generator->generate($info);
        $this->assertType("string", $result);
        $result = new \SimpleXMLElement($result);
        $namespaces = $result->getNamespaces(true);
        $this->assertSame($extension->getNamespace(), $namespaces[$extensionName]);
        $this->assertSame(
            "https://example.com/comment?item=1",
            (string) $result->channel->item->children($extensionNamespace)->$elementName1
        );
        $this->assertSame(
            "https://example.com/rss/item/1",
            (string) $result->channel->item->children($extensionNamespace)->$elementName2
        );
    }
}
