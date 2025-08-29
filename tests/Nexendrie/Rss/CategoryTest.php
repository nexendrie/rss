<?php
declare(strict_types=1);

namespace Nexendrie\Rss;

use Tester\Assert;

require __DIR__ . "/../../bootstrap.php";

/**
 * @author Jakub Konečný
 * @testCase
 */
final class CategoryTest extends \Tester\TestCase
{
    public function testAppendToXml(): void
    {
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
