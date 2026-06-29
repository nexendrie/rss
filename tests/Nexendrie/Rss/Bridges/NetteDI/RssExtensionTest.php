<?php
declare(strict_types=1);

namespace Nexendrie\Rss\Bridges\NetteDI;

use MyTester\Attributes\AfterTest;
use MyTester\Attributes\BeforeTestSuite;
use MyTester\Attributes\RequiresPhpVersion;
use MyTester\Attributes\TestSuite;
use Nexendrie\Rss\Extensions\TestExtension;
use Nexendrie\Rss\InvalidRssExtensionException;
use Nexendrie\Rss\Generator;

#[TestSuite("RssExtension")]
#[RequiresPhpVersion("8.4.0")]
final class RssExtensionTest extends \MyTester\TestCase
{
    use \MyTester\Bridges\NetteDI\TCompiledContainer;

    #[AfterTest]
    #[BeforeTestSuite]
    public function rebuildContainer(): void
    {
        $this->refreshContainer();
    }

    public function testShortenDescription(): void
    {
        /** @var Generator $generator */
        $generator = $this->getService(Generator::class);
        $this->assertNotSame("", $generator->shortenDescription);
        $this->refreshContainer(["rss" => [
            "shortenDescription" => 15,
        ]]);
        /** @var Generator $generator */
        $generator = $this->getService(Generator::class);
        $this->assertSame(15, $generator->shortenDescription);
    }

    public function testDateTimeFormat(): void
    {
        /** @var Generator $generator */
        $generator = $this->getService(Generator::class);
        $this->assertNotSame("", $generator->dateTimeFormat);
        $this->refreshContainer(["rss" => [
            "dateTimeFormat" => "Y/m/d",
        ]]);
        /** @var Generator $generator */
        $generator = $this->getService(Generator::class);
        $this->assertSame("Y/m/d", $generator->dateTimeFormat);
    }

    public function testTemplate(): void
    {
        /** @var Generator $generator */
        $generator = $this->getService(Generator::class);
        $this->assertNotSame("", $generator->template);
        $this->assertThrowsException(function () {
            $this->refreshContainer(["rss" => [
                "template" => "abc",
            ]]);
            /** @var Generator $generator */
            $generator = $this->getService(Generator::class);
            $this->assertSame("abc", $generator->template);
        }, \RuntimeException::class);
        $filename = __DIR__ . "/../../template.xml";
        $this->refreshContainer(["rss" => [
            "template" => $filename,
        ]]);
        /** @var Generator $generator */
        $generator = $this->getService(Generator::class);
        $this->assertSame($filename, $generator->template);
    }

    public function testExtensions(): void
    {
        $this->assertThrowsException(function () {
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
        $this->assertCount(1, $generator->extensions->getItems(["%class%" => TestExtension::class]));
    }
}
