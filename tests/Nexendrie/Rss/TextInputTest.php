<?php
declare(strict_types=1);

namespace Nexendrie\Rss;

use MyTester\Attributes\Group;
use MyTester\Attributes\TestSuite;
use ValueError;

#[TestSuite("TextInput")]
#[Group("elements")]
final class TextInputTest extends \MyTester\TestCase
{
    public function testAppendToXml(): void
    {
        $textInput = new TextInput("title", "description", "name", "https://example.com/textInput");
        $xml = new \SimpleXMLElement("<test></test>");
        $textInput->appendToXml($xml);
        $this->assertSame($textInput->title, (string) $xml->textInput->title);
        $this->assertSame($textInput->name, (string) $xml->textInput->name);
        $this->assertSame($textInput->description, (string) $xml->textInput->description);
        $this->assertSame($textInput->link, (string) $xml->textInput->link);
        $this->assertThrowsException(static function () {
            new TextInput("title", "description", "name", "test");
        }, ValueError::class);
    }
}
