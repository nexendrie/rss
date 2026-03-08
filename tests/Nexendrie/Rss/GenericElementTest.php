<?php
declare(strict_types=1);

namespace Nexendrie\Rss;

use Tester\Assert;

require __DIR__ . "/../../bootstrap.php";

/**
 * @author Jakub Konečný
 * @testCase
 */
final class GenericElementTest extends \Tester\TestCase
{
    public function testAppendToXml(): void
    {
        $element = new GenericElement("abc", "");
        $xml = new \SimpleXMLElement("<test></test>");
        $element->appendToXml($xml);
        Assert::same("", (string) $xml->abc);

        $element->value = "def";
        $element->appendToXml($xml);
        Assert::same("def", (string) $xml->abc);

        $element = new GenericElement("abc", ["abc", "", "def",]);
        $xml = new \SimpleXMLElement("<test></test>");
        $element->appendToXml($xml);
        Assert::same("abc", (string) $xml->abc[0]);
        Assert::same("def", (string) $xml->abc[1]);
    }
}

$test = new GenericElementTest();
$test->run();
