<?php
declare(strict_types=1);

namespace Nexendrie\Rss;

use MyTester\Attributes\Group;
use MyTester\Attributes\TestSuite;

#[TestSuite("Cloud")]
#[Group("elements")]
final class CloudTest extends \MyTester\TestCase
{
    public function testPort(): void
    {
        $cloud = new Cloud("test.com", 80, "/test", "test.a", CloudProtocol::HttpPost);
        $cloud->port = 1;
        $this->assertSame(1, $cloud->port);
    }

    public function testPath(): void
    {
        $cloud = new Cloud("test.com", 80, "/test", "test.a", CloudProtocol::HttpPost);
        $cloud->path = "/abc";
        $this->assertSame("/abc", $cloud->path);
        $this->assertThrowsException(static function () use ($cloud) {
            $cloud->path = "abc";
        }, \InvalidArgumentException::class);
    }

    public function testAppendToXml(): void
    {
        $cloud = new Cloud("test.com", 80, "/test", "test.a", CloudProtocol::HttpPost);
        $xml = new \SimpleXMLElement("<test></test>");
        $cloud->appendToXml($xml);
        $this->assertSame($cloud->domain, (string) $xml->cloud["domain"]);
        $this->assertSame((string) $cloud->port, (string) $xml->cloud["port"]);
        $this->assertSame($cloud->path, (string) $xml->cloud["path"]);
        $this->assertSame($cloud->registerProcedure, (string) $xml->cloud["registerProcedure"]);
        $this->assertSame($cloud->protocol->value, (string) $xml->cloud["protocol"]);
    }
}
