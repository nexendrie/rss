<?php
declare(strict_types=1);

namespace Nexendrie\Rss\Bridges\NetteDI;

use Tester\Assert;
use Nexendrie\Rss\Generator;

require __DIR__ . "/../../../../bootstrap.php";

final class RssExtensionTest extends \Tester\TestCase {
  use \Testbench\TCompiledContainer;

  public function testShortenDescription() {
    Assert::exception(function() {
      $this->refreshContainer(["rss" => [
        "shortenDescription" => "abc",
      ]]);
    }, \Nette\Utils\AssertionException::class);
    $this->refreshContainer(["rss" => [
      "shortenDescription" => 15,
    ]]);
    /** @var Generator $generator */
    $generator = $this->getService(Generator::class);
    Assert::same(15, $generator->shortenDescription);
  }

  public function testDateTimeFormat() {
    Assert::exception(function() {
      $this->refreshContainer(["rss" => [
        "dateTimeFormat" => 123,
      ]]);
    }, \Nette\Utils\AssertionException::class);
    $this->refreshContainer(["rss" => [
      "dateTimeFormat" => "Y/m/d",
    ]]);
    /** @var Generator $generator */
    $generator = $this->getService(Generator::class);
    Assert::same("Y/m/d", $generator->dateTimeFormat);
  }

  public function testTemplate() {
    Assert::exception(function() {
      $this->refreshContainer(["rss" => [
        "template" => 123,
      ]]);
    }, \Nette\Utils\AssertionException::class);
    Assert::exception(function() {
      $this->refreshContainer(["rss" => [
        "template" => "abc",
      ]]);
      /** @var Generator $generator */
      $generator = $this->getService(Generator::class);
      Assert::same("abc", $generator->template);
    }, \RuntimeException::class);
    $filename = __DIR__ . "/../../template.xml";
    $this->refreshContainer(["rss" => [
      "template" => $filename,
    ]]);
    /** @var Generator $generator */
    $generator = $this->getService(Generator::class);
    Assert::same($filename, $generator->template);
  }
}

$test = new RssExtensionTest();
$test->run();
?>