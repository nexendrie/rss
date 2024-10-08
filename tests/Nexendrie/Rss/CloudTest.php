<?php
declare(strict_types=1);

namespace Nexendrie\Rss;

use Tester\Assert;

require __DIR__ . "/../../bootstrap.php";

/**
 * @author Jakub Konečný
 * @testCase
 */
final class CloudTest extends \Tester\TestCase {
  public function testPort(): void {
    $cloud = new Cloud("test.com", 80, "/test", "test.a", "http-post");
    $cloud->port = 1;
    Assert::same(1, $cloud->port);
  }

  public function testPath(): void {
    $cloud = new Cloud("test.com", 80, "/test", "test.a", "http-post");
    $cloud->path = "/abc";
    Assert::same("/abc", $cloud->path);
    Assert::exception(function() use($cloud) {
      $cloud->path = "abc";
    }, \InvalidArgumentException::class);
  }

  public function testRegisterProtocol(): void {
    $cloud = new Cloud("test.com", 80, "/test", "test.a", "http-post");
    $cloud->protocol = "soap";
    Assert::same("soap", $cloud->protocol);
    Assert::exception(function() use($cloud) {
      $cloud->protocol = "abc";
    }, \InvalidArgumentException::class);
  }

  public function testAppendToXml(): void {
    $cloud = new Cloud("test.com", 80, "/test", "test.a", "http-post");
    $xml = new \SimpleXMLElement("<test></test>");
    $cloud->appendToXml($xml);
    Assert::same($cloud->domain, (string) $xml->cloud["domain"]);
    Assert::same((string) $cloud->port, (string) $xml->cloud["port"]);
    Assert::same($cloud->path, (string) $xml->cloud["path"]);
    Assert::same($cloud->registerProcedure, (string) $xml->cloud["registerProcedure"]);
    Assert::same($cloud->protocol, (string) $xml->cloud["protocol"]);
  }
}

$test = new CloudTest();
$test->run();
?>