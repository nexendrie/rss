<?php
declare(strict_types=1);

namespace Nexendrie\Rss\Extensions;

use DateTime;
use Nexendrie\Rss\Collection;
use Nexendrie\Rss\Generator;
use Nexendrie\Rss\RssChannelItem;
use Tester\Assert;

require __DIR__ . "/../../../bootstrap.php";

/**
 * @author Jakub Konečný
 * @testCase
 */
final class BlogChannelTest extends \Tester\TestCase
{
    public function testGetName(): void
    {
        $extension = new BlogChannel();
        Assert::same("blogChannel", $extension->getName());
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
        Assert::type("string", $result);
        $result = new \SimpleXMLElement($result);
        $namespaces = $result->getNamespaces(true);
        Assert::same($extension->getNamespace(), $namespaces[$extensionName]);
        Assert::same(
            "https://example.com/blogRoll.opml",
            (string) $result->channel->item->children($extensionNamespace)->$elementName1
        );
        Assert::same(
            "https://example.com/user/1/subscriptions.opml",
            (string) $result->channel->item->children($extensionNamespace)->$elementName2
        );
    }
}

$test = new BlogChannelTest();
$test->run();
