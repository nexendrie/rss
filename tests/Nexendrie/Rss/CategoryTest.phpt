<?php
declare(strict_types=1);

namespace Nexendrie\Rss;

use Tester\Assert;

require __DIR__ . "/../../bootstrap.php";

final class CategoryTest extends \Tester\TestCase {
  public function testIdentifier() {
    $category = new Category("id");
    $category->identifier = "abc";
    Assert::same("abc", $category->identifier);
  }

  public function testDomain() {
    $category = new Category("id");
    $category->domain = "abc";
    Assert::same("abc", $category->domain);
  }

  public function testAppendToXml() {
    $category = new Category("abc");
    $xml = new \SimpleXMLElement("<test></test>");
    $category->appendToXml($xml);
    Assert::same($category->identifier, (string) $xml->category);
    Assert::same("", (string) $xml->category["domain"]);
    $category = new Category("def", "domain");
    $xml = new \SimpleXMLElement("<test></test>");
    $category->appendToXml($xml);
    Assert::same($category->identifier, (string) $xml->category);
    Assert::same($category->domain, (string) $xml->category["domain"]);
  }
}

$test = new CategoryTest();
$test->run();
?>