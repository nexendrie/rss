<?php
declare(strict_types=1);

namespace Nexendrie\Rss\Extensions;

use Nexendrie\Rss\Collection;
use Nexendrie\Rss\Generator;
use Nexendrie\Rss\RssChannelItem;
use Tester\Assert;

require __DIR__ . "/../../../bootstrap.php";

/**
 * @author Jakub Konečný
 * @testCase
 */
final class ContentTest extends \Tester\TestCase
{
    public function testExtension(): void
    {
        $generator = new Generator();
        $generator->extensions[] = $extension = new Content();
        $extensionName = $extension->getName();
        $extensionNamespace = $extension->getNamespace();
        $elementName = Content::ELEMENT_ENCODED;
        $info = [
            "title" => "Nexendrie RSS", "link" => "https://gitlab.com/nexendrie/rss/",
            "description" => "News for package nexendrie/rss",
        ];
        $generator->dataSource = function () use ($extensionName, $elementName) {
            $collection = new Collection();
            $collection[] = new RssChannelItem([
                "title" => "Item 1", "description" => "Item 1 description", "link" => "", "pubDate" => 123,
                "$extensionName:$elementName" => "def",
            ]);
            return $collection;
        };
        $result = $generator->generate($info);
        Assert::type("string", $result);
        $result = new \SimpleXMLElement($result);
        $namespaces = $result->getNamespaces(true);
        Assert::same($extension->getNamespace(), $namespaces[$extensionName]);
        Assert::same("def", (string) $result->channel->item->children($extensionNamespace, false)->$elementName);
    }
}

$test = new ContentTest();
$test->run();
