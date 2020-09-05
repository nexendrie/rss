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
  public function testTitle() {
    $textInput = new TextInput("title", "description", "name", "link");
    $textInput->title = "abc";
    Assert::same("abc", $textInput->title);
  }

  public function testName() {
    $textInput = new TextInput("title", "description", "name", "link");
    $textInput->name = "abc";
    Assert::same("abc", $textInput->name);
  }

  public function testDescription() {
    $textInput = new TextInput("title", "description", "name", "link");
    $textInput->description = "abc";
    Assert::same("abc", $textInput->description);
  }

  public function testLink() {
    $textInput = new TextInput("title", "description", "name", "link");
    $textInput->link = "abc";
    Assert::same("abc", $textInput->link);
  }

  public function testAppendToXml() {
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