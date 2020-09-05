<?php
declare(strict_types=1);

namespace Nexendrie\Rss;

use Tester\Assert;

require __DIR__ . "/../../bootstrap.php";

/**
 * @author Jakub Konečný
 * @testCase
 */
final class GenericElementTest extends \Tester\TestCase {
  public function testName() {
    $element = new GenericElement("abc", "");
    Assert::same("abc", $element->name);
  }

  public function testValue() {
    $element = new GenericElement("abc", "");
    Assert::same("", $element->value);
    $element->value = "def";
    Assert::same("def", $element->value);
  }

  public function testAppendToXml() {
    $element = new GenericElement("abc", "");
    $xml = new \SimpleXMLElement("<test></test>");
    $element->appendToXml($xml);
    Assert::same("", (string) $xml->{$element->name});
    $element->value = "def";
    $element->appendToXml($xml);
    Assert::same("def", (string) $xml->{$element->name});
  }
}

$test = new GenericElementTest();
$test->run();
?>