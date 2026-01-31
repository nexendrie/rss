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
final class CreativeCommonsTest extends \Tester\TestCase
{
    public function testGetName(): void
    {
        $extension = new CreativeCommons();
        Assert::same("creativeCommons", $extension->getName());
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
                "title" => "Item 1", "description" => "Item 1 description", "link" => "", "pubDate" => new DateTime(),
                "$extensionName:$elementName" => ["abc", "", "def",],
            ]);
            return $collection;
        };
        $result = $generator->generate($info);
        Assert::type("string", $result);
        $result = new \SimpleXMLElement($result);
        $namespaces = $result->getNamespaces(true);
        Assert::same($extension->getNamespace(), $namespaces[$extensionName]);
        Assert::count(2, $result->channel->item->children($extensionNamespace)->$elementName);
        Assert::same("abc", (string) $result->channel->item->children($extensionNamespace)->$elementName[0]);
        Assert::same("def", (string) $result->channel->item->children($extensionNamespace)->$elementName[1]);
    }
}

$test = new CreativeCommonsTest();
$test->run();
