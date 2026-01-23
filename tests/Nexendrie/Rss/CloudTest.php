<?php
declare(strict_types=1);

namespace Nexendrie\Rss;

use Tester\Assert;

require __DIR__ . "/../../bootstrap.php";

/**
 * @author Jakub Konečný
 * @testCase
 */
final class CloudTest extends \Tester\TestCase
{
    public function testPort(): void
    {
        $cloud = new Cloud("test.com", 80, "/test", "test.a", CloudProtocol::HttpPost);
        $cloud->port = 1;
        Assert::same(1, $cloud->port);
    }

    public function testPath(): void
    {
        $cloud = new Cloud("test.com", 80, "/test", "test.a", CloudProtocol::HttpPost);
        $cloud->path = "/abc";
        Assert::same("/abc", $cloud->path);
        Assert::exception(static function () use ($cloud) {
            $cloud->path = "abc";
        }, \InvalidArgumentException::class);
    }

    public function testAppendToXml(): void
    {
        $cloud = new Cloud("test.com", 80, "/test", "test.a", CloudProtocol::HttpPost);
        $xml = new \SimpleXMLElement("<test></test>");
        $cloud->appendToXml($xml);
        Assert::same($cloud->domain, (string) $xml->cloud["domain"]);
        Assert::same((string) $cloud->port, (string) $xml->cloud["port"]);
        Assert::same($cloud->path, (string) $xml->cloud["path"]);
        Assert::same($cloud->registerProcedure, (string) $xml->cloud["registerProcedure"]);
        Assert::same($cloud->protocol->value, (string) $xml->cloud["protocol"]);
    }
}

$test = new CloudTest();
$test->run();
