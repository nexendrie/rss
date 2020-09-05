<?php
declare(strict_types=1);

namespace Nexendrie\Rss;

use Tester\Assert;

require __DIR__ . "/../../bootstrap.php";

/**
 * @author Jakub Konečný
 * @testCase
 */
final class EnclosureTest extends \Tester\TestCase {
  public function testUrl() {
    $enclosure = new Enclosure("url", 15, "text/plain");
    $enclosure->url = "abc";
    Assert::same("abc", $enclosure->url);
  }

  public function testLength() {
    $enclosure = new Enclosure("url", 15, "text/plain");
    $enclosure->length = 1;
    Assert::same(1, $enclosure->length);
  }

  public function testType() {
    $enclosure = new Enclosure("url", 15, "text/plain");
    $enclosure->type = "application/xml";
    Assert::same("application/xml", $enclosure->type);
  }

  public function testAppendToXml() {
    $enclosure = new Enclosure("url", 15, "text/plain");
    $xml = new \SimpleXMLElement("<test></test>");
    $enclosure->appendToXml($xml);
    Assert::same($enclosure->url, (string) $xml->enclosure["url"]);
    Assert::same((string) $enclosure->length, (string) $xml->enclosure["length"]);
    Assert::same($enclosure->type, (string) $xml->enclosure["type"]);
  }
}

$test = new EnclosureTest();
$test->run();
?>