<?php
declare(strict_types=1);

namespace Nexendrie\Rss;

use Tester\Assert;
use ValueError;

require __DIR__ . "/../../bootstrap.php";

/**
 * @author Jakub Konečný
 * @testCase
 */
final class EnclosureTest extends \Tester\TestCase
{
    public function testAppendToXml(): void
    {
        $enclosure = new Enclosure("https://example.com/music/test.mp3", 15, "audio/mpeg");
        $xml = new \SimpleXMLElement("<test></test>");
        $enclosure->appendToXml($xml);
        Assert::same($enclosure->url, (string) $xml->enclosure["url"]);
        Assert::same((string) $enclosure->length, (string) $xml->enclosure["length"]);
        Assert::same($enclosure->type, (string) $xml->enclosure["type"]);
        Assert::exception(static function () {
            new Enclosure("test", 15, "text/plain");
        }, ValueError::class);
    }
}

$test = new EnclosureTest();
$test->run();
