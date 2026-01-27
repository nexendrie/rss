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
final class SlashTest extends \Tester\TestCase
{
    public function testGetName(): void
    {
        $extension = new Slash();
        Assert::same("slash", $extension->getName());
    }

    public function testExtension(): void
    {
        $generator = new Generator();
        $generator->extensions[] = $extension = new Slash();
        $extensionName = $extension->getName();
        $extensionNamespace = $extension->getNamespace();
        $elementName1 = Slash::ELEMENT_SECTION;
        $elementName2 = Slash::ELEMENT_DEPARTMENT;
        $elementName3 = Slash::ELEMENT_COMMENTS;
        $elementName4 = Slash::ELEMENT_HIT_PARADE;
        $info = [
            "title" => "Nexendrie RSS", "link" => "https://gitlab.com/nexendrie/rss/",
            "description" => "News for package nexendrie/rss",
        ];
        $generator->dataSource = static function () use ($extensionName, $elementName1, $elementName2, $elementName3, $elementName4) {
            $collection = new Collection();
            $collection[] = new RssChannelItem([
                "title" => "Item 1", "description" => "Item 1 description", "link" => "", "pubDate" => 123,
                "$extensionName:$elementName1" => "abc", "$extensionName:$elementName2" => "def",
                "$extensionName:$elementName3" => 1, "$extensionName:$elementName4" => "1,2,3",
            ]);
            return $collection;
        };
        $result = $generator->generate($info);
        Assert::type("string", $result);
        $result = new \SimpleXMLElement($result);
        $namespaces = $result->getNamespaces(true);
        Assert::same($extension->getNamespace(), $namespaces[$extensionName]);
        Assert::same("abc", (string) $result->channel->item->children($extensionNamespace, false)->$elementName1);
        Assert::same("def", (string) $result->channel->item->children($extensionNamespace, false)->$elementName2);
        Assert::same("1", (string) $result->channel->item->children($extensionNamespace, false)->$elementName3);
        Assert::same("1,2,3", (string) $result->channel->item->children($extensionNamespace, false)->$elementName4);
    }
}

$test = new SlashTest();
$test->run();
