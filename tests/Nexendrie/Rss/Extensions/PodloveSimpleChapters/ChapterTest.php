<?php
declare(strict_types=1);

namespace Nexendrie\Rss\Extensions\PodloveSimpleChapters;

use MyTester\Attributes\Group;
use MyTester\Attributes\TestSuite;
use MyTester\TestCase;
use Nexendrie\Rss\Extensions\PodloveSimpleChapters;
use SimpleXMLElement;
use ValueError;

#[TestSuite("Chapter")]
#[Group("extensions")]
#[Group("podloveSimpleChapters")]
final class ChapterTest extends TestCase
{
    public function testStart(): void
    {
        $chapter = new Chapter(0, "");
        $this->assertSame(0, $chapter->start);

        $chapter->start = 263;
        $this->assertSame(263, $chapter->start);

        $chapter->start = 5752.5000;
        $this->assertSame(5752.5000, $chapter->start);

        $chapter->start = "07:48";
        $this->assertSame("07:48", $chapter->start);

        $chapter->start = "35:12.250";
        $this->assertSame("35:12.250", $chapter->start);

        $chapter->start = "01:35:52";
        $this->assertSame("01:35:52", $chapter->start);

        $chapter->start = "05:12:03.5";
        $this->assertSame("05:12:03.5", $chapter->start);

        $this->assertThrowsException(static function () use ($chapter): void {
            $chapter->start = "abc";
        }, ValueError::class);

        $this->assertThrowsException(static function () use ($chapter): void {
            $chapter->start = "78:00";
        }, ValueError::class);

        $this->assertThrowsException(static function () use ($chapter): void {
            $chapter->start = "05:75";
        }, ValueError::class);

        $this->assertThrowsException(static function () use ($chapter): void {
            $chapter->start = "05:59:61";
        }, ValueError::class);
    }

    public function testHref(): void
    {
        $chapter = new Chapter(0, "", href: "https://example.com");
        $this->assertSame("https://example.com", $chapter->href);

        $this->assertThrowsException(static function () use ($chapter): void {
            $chapter->href = "abc";
        }, ValueError::class);
    }

    public function testImage(): void
    {
        $chapter = new Chapter(0, "", image: "https://example.com/rss/image.jpeg");
        $this->assertSame("https://example.com/rss/image.jpeg", $chapter->image);

        $this->assertThrowsException(static function () use ($chapter): void {
            $chapter->image = "abc";
        }, ValueError::class);
    }

    public function testAppendToXml(): void
    {
        $chaptersXml = "<psc:chapters xmlns:psc='" . PodloveSimpleChapters::NAMESPACE . "'></psc:chapters>";
        $xml = new SimpleXMLElement($chaptersXml);

        $chapter = new Chapter(0, "abc");
        $chapter->appendToXml($xml);
        $this->assertSame(
            "0",
            (string) $xml->children(PodloveSimpleChapters::NAMESPACE)->chapter->attributes()["start"]
        );
        $this->assertSame(
            "abc",
            (string) $xml->children(PodloveSimpleChapters::NAMESPACE)->chapter->attributes()["title"]
        );

        $chapter = new Chapter("11:52", "def", "https://example.com", "https://example.com/rss/image.jpeg");
        $chapter->appendToXml($xml);
        $this->assertSame(
            "0",
            (string) $xml->children(PodloveSimpleChapters::NAMESPACE)->chapter[0]->attributes()["start"]
        );
        $this->assertSame(
            "abc",
            (string) $xml->children(PodloveSimpleChapters::NAMESPACE)->chapter[0]->attributes()["title"]
        );
        $this->assertSame(
            "11:52",
            (string) $xml->children(PodloveSimpleChapters::NAMESPACE)->chapter[1]->attributes()["start"]
        );
        $this->assertSame(
            "def",
            (string) $xml->children(PodloveSimpleChapters::NAMESPACE)->chapter[1]->attributes()["title"]
        );
        $this->assertSame(
            "https://example.com",
            (string) $xml->children(PodloveSimpleChapters::NAMESPACE)->chapter[1]->attributes()["href"]
        );
        $this->assertSame(
            "https://example.com/rss/image.jpeg",
            (string) $xml->children(PodloveSimpleChapters::NAMESPACE)->chapter[1]->attributes()["image"]
        );
    }
}
