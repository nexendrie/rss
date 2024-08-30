<?php
declare(strict_types=1);

namespace Nexendrie\Rss;

use Tester\Assert;

require __DIR__ . "/../../bootstrap.php";

/**
 * @author Jakub Konečný
 * @testCase
 */
final class ImageTest extends \Tester\TestCase {
  public function testWidth() {
    $image = new Image("url", "title", "link");
    $image->width = 1;
    Assert::same(1, $image->width);
  }

  public function testHeight() {
    $image = new Image("url", "title", "link");
    $image->height = 1;
    Assert::same(1, $image->height);
  }

  public function testAppendToXml() {
    $image = new Image("url", "title", "link");
    $xml = new \SimpleXMLElement("<test></test>");
    $image->appendToXml($xml);
    Assert::same($image->url, (string) $xml->image->url);
    Assert::same($image->title, (string) $xml->image->title);
    Assert::same($image->link, (string) $xml->image->link);
    Assert::same("", (string) $xml->image->width);
    Assert::same("", (string) $xml->image->height);
    Assert::same("", (string) $xml->image->description);
    $image = new Image("url", "title", "link", "description");
    $image->width = 1;
    $image->height = 1;
    $xml = new \SimpleXMLElement("<test></test>");
    $image->appendToXml($xml);
    Assert::same($image->url, (string) $xml->image->url);
    Assert::same($image->title, (string) $xml->image->title);
    Assert::same($image->link, (string) $xml->image->link);
    Assert::same((string) $image->width, (string) $xml->image->width);
    Assert::same((string) $image->height, (string) $xml->image->height);
    Assert::same($image->description, (string) $xml->image->description);
  }
}

$test = new ImageTest();
$test->run();
?>