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
final class DublinCoreTest extends \Tester\TestCase
{
    public function testGetName(): void
    {
        $extension = new DublinCore();
        Assert::same("dc", $extension->getName());
    }

    public function testExtension(): void
    {
        $generator = new Generator();
        $generator->extensions[] = $extension = new DublinCore();
        $extensionName = $extension->getName();
        $extensionNamespace = $extension->getNamespace();
        $elementName1 = DublinCore::ELEMENT_TITLE;
        $elementName2 = DublinCore::ELEMENT_CREATOR;
        $info = [
            "title" => "Nexendrie RSS", "link" => "https://gitlab.com/nexendrie/rss/",
            "description" => "News for package nexendrie/rss",
        ];
        $generator->dataSource = static function () use ($extensionName, $elementName1, $elementName2) {
            $collection = new Collection();
            $collection[] = new RssChannelItem([
                "title" => "Item 1", "description" => "Item 1 description", "link" => "", "pubDate" => new DateTime(),
                "$extensionName:$elementName1" => "abc", "$extensionName:$elementName2" => "def",
            ]);
            return $collection;
        };
        $result = $generator->generate($info);
        Assert::type("string", $result);
        $result = new \SimpleXMLElement($result);
        $namespaces = $result->getNamespaces(true);
        Assert::same($extension->getNamespace(), $namespaces[$extensionName]);
        Assert::same("abc", (string) $result->channel->item->children($extensionNamespace)->$elementName1);
        Assert::same("def", (string) $result->channel->item->children($extensionNamespace)->$elementName2);
    }
}

$test = new DublinCoreTest();
$test->run();
