<?php
declare(strict_types=1);

namespace Nexendrie\Rss;

use SimpleXMLElement;
use Tester\Assert;

require __DIR__ . "/../../bootstrap.php";

/**
 * @author Jakub Konečný
 * @testCase
 */
final class GuidTest extends \Tester\TestCase
{
    public function testAppendToXml(): void
    {
        $guid = new Guid("abc");
        $xml = new SimpleXMLElement("<test></test>");
        $guid->appendToXml($xml);
        Assert::same($guid->value, (string) $xml->guid);
        Assert::same("", (string) $xml->guid["isPermalink"]);

        $guid = new Guid("abc", true);
        $xml = new SimpleXMLElement("<test></test>");
        $guid->appendToXml($xml);
        Assert::same($guid->value, (string) $xml->guid);
        Assert::same("true", (string) $xml->guid["isPermalink"]);

        $guid = new Guid("abc", false);
        $xml = new SimpleXMLElement("<test></test>");
        $guid->appendToXml($xml);
        Assert::same($guid->value, (string) $xml->guid);
        Assert::same("false", (string) $xml->guid["isPermalink"]);
    }
}

$test = new GuidTest();
$test->run();
