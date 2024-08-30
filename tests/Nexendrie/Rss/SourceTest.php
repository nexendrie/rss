<?php
declare(strict_types=1);

namespace Nexendrie\Rss;

use Tester\Assert;

require __DIR__ . "/../../bootstrap.php";

/**
 * @author Jakub Konečný
 * @testCase
 */
final class SourceTest extends \Tester\TestCase {
  public function testAppendToXml() {
    $source = new Source("", "title");
    $xml = new \SimpleXMLElement("<test></test>");
    $source->appendToXml($xml);
    Assert::same("", (string) $xml->source);
    Assert::same("", (string) $xml->source["url"]);
    $source = new Source("url", "");
    $xml = new \SimpleXMLElement("<test></test>");
    $source->appendToXml($xml);
    Assert::same("", (string) $xml->source);
    Assert::same($source->url, (string) $xml->source["url"]);
    $source = new Source("url", "title");
    $xml = new \SimpleXMLElement("<test></test>");
    $source->appendToXml($xml);
    Assert::same($source->title, (string) $xml->source);
    Assert::same($source->url, (string) $xml->source["url"]);
  }
}

$test = new SourceTest();
$test->run();
?>