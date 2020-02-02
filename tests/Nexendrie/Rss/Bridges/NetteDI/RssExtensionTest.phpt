<?php
declare(strict_types=1);

namespace Nexendrie\Rss\Bridges\NetteDI;

use Nexendrie\Rss\Extensions\TestExtension;
use Nexendrie\Rss\InvalidRssExtensionException;
use Tester\Assert;
use Nexendrie\Rss\Generator;

require __DIR__ . "/../../../../bootstrap.php";

final class RssExtensionTest extends \Tester\TestCase {
  use \Testbench\TCompiledContainer;

  public function testShortenDescription() {
    /** @var Generator $generator */
    $generator = $this->getService(Generator::class);
    Assert::notSame("", $generator->shortenDescription);
    $this->refreshContainer(["rss" => [
      "shortenDescription" => 15,
    ]]);
    /** @var Generator $generator */
    $generator = $this->getService(Generator::class);
    Assert::same(15, $generator->shortenDescription);
  }

  public function testDateTimeFormat() {
    /** @var Generator $generator */
    $generator = $this->getService(Generator::class);
    Assert::notSame("", $generator->dateTimeFormat);
    $this->refreshContainer(["rss" => [
      "dateTimeFormat" => "Y/m/d",
    ]]);
    /** @var Generator $generator */
    $generator = $this->getService(Generator::class);
    Assert::same("Y/m/d", $generator->dateTimeFormat);
  }

  public function testTemplate() {
    /** @var Generator $generator */
    $generator = $this->getService(Generator::class);
    Assert::notSame("", $generator->template);
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

  public function testExtensions() {
    Assert::exception(function() {
      $this->refreshContainer(["rss" => [
        "extensions" => [
          \stdClass::class,
        ],
      ]]);
    }, InvalidRssExtensionException::class);
    $this->refreshContainer(["rss" => [
      "extensions" => [TestExtension::class],
    ]]);
    /** @var Generator $generator */
    $generator = $this->getService(Generator::class);
    Assert::count(1, $generator->extensions->getItems(["%class%" => TestExtension::class]));
  }
}

$test = new RssExtensionTest();
$test->run();
?>