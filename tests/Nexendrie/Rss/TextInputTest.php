<?php
declare(strict_types=1);

namespace Nexendrie\Rss;

use Tester\Assert;

require __DIR__ . "/../../bootstrap.php";

/**
 * @author Jakub Konečný
 * @testCase
 */
final class TextInputTest extends \Tester\TestCase {
  public function testAppendToXml(): void {
    $textInput = new TextInput("title", "description", "name", "link");
    $xml = new \SimpleXMLElement("<test></test>");
    $textInput->appendToXml($xml);
    Assert::same($textInput->title, (string) $xml->textInput->title);
    Assert::same($textInput->name, (string) $xml->textInput->name);
    Assert::same($textInput->description, (string) $xml->textInput->description);
    Assert::same($textInput->link, (string) $xml->textInput->link);
  }
}

$test = new TextInputTest();
$test->run();
?>