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
final class SyndicationTest extends \Tester\TestCase {
  public function testExtension() {
    $generator = new Generator();
    $generator->extensions[] = $extension = new Syndication();
    $extensionName = $extension->getName();
    $extensionNamespace = $extension->getNamespace();
    $elementName1 = Syndication::ELEMENT_UPDATE_PERIOD;
    $elementName2 = Syndication::ELEMENT_UPDATE_FREQUENCY;
    $elementName3 = Syndication::ELEMENT_UPDATE_BASE;
    $info = [
      "title" => "Nexendrie RSS", "link" => "https://gitlab.com/nexendrie/rss/",
      "description" => "News for package nexendrie/rss", "$extensionName:$elementName1" => Syndication::UPDATE_PERIOD_HOURLY,
      "$extensionName:$elementName2" => 1, "$extensionName:$elementName3" => "abc",
    ];
    $generator->dataSource = function() use ($extensionName) {
      $collection = new Collection();
      $collection[] = new RssChannelItem([
        "title" => "Item 1", "description" => "Item 1 description", "link" => "", "pubDate" => 123,
      ]);
      return $collection;
    };
    $result = $generator->generate($info);
    Assert::type("string", $result);
    $result = new \SimpleXMLElement($result);
    $namespaces = $result->getNamespaces(true);
    Assert::same($extension->getNamespace(), $namespaces[$extensionName]);
    Assert::same(Syndication::UPDATE_PERIOD_HOURLY, (string) $result->channel->children($extensionNamespace, false)->$elementName1);
    Assert::same("1", (string) $result->channel->children($extensionNamespace, false)->$elementName2);
    Assert::same("abc", (string) $result->channel->children($extensionNamespace, false)->$elementName3);
  }
}

$test = new SyndicationTest();
$test->run();
?>