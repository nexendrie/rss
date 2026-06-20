<?php
declare(strict_types=1);

namespace Nexendrie\Rss\Extensions;

use DateTime;
use MyTester\Attributes\Group;
use MyTester\Attributes\TestSuite;
use Nexendrie\Rss\Collection;
use Nexendrie\Rss\Generator;
use Nexendrie\Rss\RssChannelItem;

#[TestSuite("BlogChannel")]
#[Group("extensions")]
final class BlogChannelTest extends \MyTester\TestCase
{
    public function testGetName(): void
    {
        $extension = new BlogChannel();
        $this->assertSame("blogChannel", $extension->getName());
    }

    public function testExtension(): void
    {
        $generator = new Generator();
        $generator->extensions[] = $extension = new BlogChannel();
        $extensionName = $extension->getName();
        $extensionNamespace = $extension->getNamespace();
        $elementName1 = BlogChannel::ELEMENT_BLOG_ROLL;
        $elementName2 = BlogChannel::ELEMENT_MY_SUBSCRIPTIONS;
        $info = [
            "title" => "Nexendrie RSS", "link" => "https://gitlab.com/nexendrie/rss/",
            "description" => "News for package nexendrie/rss",
        ];
        $generator->dataSource = static function () use ($extensionName, $elementName1, $elementName2) {
            $collection = new Collection();
            $collection[] = new RssChannelItem([
                "title" => "Item 1", "description" => "Item 1 description", "link" => "https://example.com/item/1",
                "pubDate" => new DateTime(), "$extensionName:$elementName1" => "https://example.com/blogRoll.opml",
                "$extensionName:$elementName2" => "https://example.com/user/1/subscriptions.opml",
            ]);
            return $collection;
        };
        $result = $generator->generate($info);
        $this->assertType("string", $result);
        $result = new \SimpleXMLElement($result);
        $namespaces = $result->getNamespaces(true);
        $this->assertSame($extension->getNamespace(), $namespaces[$extensionName]);
        $this->assertSame(
            "https://example.com/blogRoll.opml",
            (string) $result->channel->item->children($extensionNamespace)->$elementName1
        );
        $this->assertSame(
            "https://example.com/user/1/subscriptions.opml",
            (string) $result->channel->item->children($extensionNamespace)->$elementName2
        );
    }
}
