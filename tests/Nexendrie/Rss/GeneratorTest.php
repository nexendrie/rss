<?php
declare(strict_types=1);

namespace Nexendrie\Rss;

use DateTime;
use Konecnyjakub\EventDispatcher\DebugEventDispatcher;
use Konecnyjakub\EventDispatcher\DummyEventDispatcher;
use MyTester\Attributes\BeforeTest;
use MyTester\Attributes\TestSuite;
use Nexendrie\Rss\Bridges\NetteApplication\RssResponse;
use Nexendrie\Rss\Events\ChannelAfterGenerate;
use Nexendrie\Rss\Events\ChannelBeforeGenerate;
use Nexendrie\Rss\Events\ItemAdded;
use Nexendrie\Rss\Extensions\RssCore\Iso639Language;
use Nexendrie\Rss\Extensions\RssCore\RssLanguage;
use Nexendrie\Rss\Extensions\RssCore\SkipDay;
use Psr\Log\NullLogger;
use Nexendrie\Rss\Extensions\TestExtension;

#[TestSuite("Generator")]
final class GeneratorTest extends \MyTester\TestCase
{
    private Generator $generator;

    #[BeforeTest]
    public function setUp(): void
    {
        $this->generator = new Generator();
    }

    private function countItems(\SimpleXMLElement $channel): int
    {
        $items = 0;
        /** @var \SimpleXMLElement $child */
        foreach ($channel->channel->children() as $child) {
            if ($child->getName() === "item") {
                $items++;
            }
        }
        return $items;
    }

    public function testEmptyChannel(): void
    {
        $info = [
            "title" => "Test", "link" => "https://gitlab.com/nexendrie/rss/", "description" => "Test RSS Channel",
        ];
        $this->generator->dataSource = static fn() => new Collection();
        $result = $this->generator->generate($info);
        $this->assertType("string", $result);
        $result = new \SimpleXMLElement($result);
        $this->assertSame("Test", (string) $result->channel->title);
        $this->assertSame("Test RSS Channel", (string) $result->channel->description);
        $this->assertSame($this->generator->docs, (string) $result->channel->docs);
        $this->assertSame($this->generator->generator, (string) $result->channel->generator);
        $this->assertSame("", (string) $result->channel->pubDate);
        $this->assertSame(0, $this->countItems($result));
    }

    public function testGenerate(): void
    {
        $info = [
            "title" => "Nexendrie RSS", "link" => "https://gitlab.com/nexendrie/rss/",
            "description" => "News for package nexendrie/rss",
        ];
        $this->generator->dataSource = static function () {
            $items = new Collection();
            $items[] = new RssChannelItem([
                "title" => "Item 1", "description" => "Item 1 description", "link" => "https://example.com/item/1",
                "pubDate" => new DateTime(),
            ]);
            return $items;
        };
        $result = $this->generator->generate($info);
        $this->assertType("string", $result);
        $result = new \SimpleXMLElement($result);
        $this->assertSame($info["title"], (string) $result->channel->title);
        $this->assertSame($info["description"], (string) $result->channel->description);
        $this->assertSame($info["link"], (string) $result->channel->link);
        $this->assertSame($this->generator->docs, (string) $result->channel->docs);
        $this->assertSame($this->generator->generator, (string) $result->channel->generator);
        $this->assertSame(1, $this->countItems($result));
        $this->assertType("string", (string) $result->channel->lastBuidDate);
        $this->assertSame("", (string) $result->channel->pubDate);
    }

    public function testInvalidDataSource(): void
    {
        $this->generator->dataSource = static fn() => [];
        $this->assertThrowsException(function () {
            $this->generator->generate(["title" => "", "link" => "", "description" => "",]);
        }, \InvalidArgumentException::class);
    }

    public function testCustomLastBuildDate(): void
    {
        $info = [
            "title" => "Nexendrie RSS", "link" => "https://gitlab.com/nexendrie/rss/",
            "description" => "News for package nexendrie/rss",
            "lastBuildDate" => static fn() => new DateTime("2024-12-31"),
        ];
        $this->generator->dataSource = static function () {
            $items = new Collection();
            $items[] = new RssChannelItem([
                "title" => "Item 1", "description" => "Item 1 description", "link" => "https://example.com/item/1",
                "pubDate" => new DateTime(),
            ]);
            return $items;
        };
        $result = new \SimpleXMLElement($this->generator->generate($info));
        $this->assertType("string", (string) $result->channel->lastBuidDate);
    }

    public function testInvalidLastBuildDate(): void
    {
        $this->generator->dataSource = static function () {
            $items = new Collection();
            $items[] = new RssChannelItem([
                "title" => "Item 1", "description" => "Item 1 description", "link" => "https://example.com/item/1",
                "pubDate" => new DateTime(),
            ]);
            return $items;
        };
        $this->assertThrowsException(function () {
            $info = [
                "title" => "Nexendrie RSS", "link" => "https://gitlab.com/nexendrie/rss/",
                "description" => "News for package nexendrie/rss", "lastBuildDate" => static fn() => "abc",
            ];
            $this->generator->generate($info);
        }, \InvalidArgumentException::class, "Callback for last build date for RSS generator has to return DateTime.");
    }

    public function testResponse(): void
    {
        $info = [
            "title" => "Nexendrie RSS", "link" => "https://gitlab.com/nexendrie/rss/",
            "description" => "News for package nexendrie/rss",
        ];
        $this->assertThrowsException(function () use ($info) {
            $this->generator->response($info);
        }, InvalidStateException::class);
        $this->generator->dataSource = static function () {
            $items = new Collection();
            $items[] = new RssChannelItem([
                "title" => "Item 1", "description" => "Item 1 description", "link" => "https://example.com/item/1",
                "pubDate" => new DateTime(),
            ]);
            return $items;
        };
        $result = $this->generator->response($info);
        $this->assertType(RssResponse::class, $result);
        $this->assertType("string", $result->source);
    }

    public function testDateTimeFormat(): void
    {
        $info = [
            "title" => "Nexendrie RSS", "link" => "https://gitlab.com/nexendrie/rss/",
            "description" => "News for package nexendrie/rss",
        ];
        $dateTimeFormat = "Y/m/d";
        $this->generator->dateTimeFormat = $dateTimeFormat;
        $this->assertSame($dateTimeFormat, $this->generator->dateTimeFormat);
        $this->generator->dataSource = static fn() => new Collection();
        $result = new \SimpleXMLElement($this->generator->generate($info));
        $this->assertSame(date($dateTimeFormat), (string) $result->channel->lastBuildDate);
    }

    public function testPubDate(): void
    {
        $info = [
            "title" => "Nexendrie RSS", "link" => "https://gitlab.com/nexendrie/rss/",
            "description" => "News for package nexendrie/rss", "pubDate" => static fn() => "abc",
        ];
        $dateTimeFormat = "Y/m/d";
        $this->generator->dateTimeFormat = $dateTimeFormat;
        $this->generator->dataSource = static function () {
            $items = new Collection();
            $items[] = new RssChannelItem([
                "title" => "Item 1", "description" => "Item 1 description", "link" => "https://example.com/item/1",
                "pubDate" => new DateTime(),
            ]);
            return $items;
        };
        $this->assertThrowsException(function () use ($info) {
            $this->generator->generate($info);
        }, \InvalidArgumentException::class, "Callback for pub date for RSS generator has to return DateTime.");
        $info["pubDate"] = static fn() => new DateTime("2024-12-31");
        $result = new \SimpleXMLElement($this->generator->generate($info));
        $this->assertSame("2024/12/31", (string) $result->channel->pubDate);
    }

    public function testOptionalThings(): void
    {
        $info = [
            "title" => "Nexendrie RSS", "link" => "https://gitlab.com/nexendrie/rss/",
            "language" => RssLanguage::English, "description" => "News for package nexendrie/rss",
            "copyright" => "Copyright 2019, Abc", "managingEditor" => "abc@test.localhost (Abc)",
            "webMaster" => "def@test.localhost", "ttl" => 60,
            "rating" => "(PICS-1.1 \"http://www.classify.org/safesurf/\" 1 r (SS~~000 1))",
            "skipDays" => [SkipDay::Monday, SkipDay::Monday, SkipDay::Sunday,], "skipHours" => [1, 1, 10],
            "image" => new Image("https://example.com/rss/image.jpeg", "title", "https://example.com"),
            "cloud" => new Cloud("test.com", 80, "/test", "test.a", CloudProtocol::HttpPost),
            "textInput" => new TextInput("title", "description", "name", "https://example.com/textInput"),
        ];
        $this->generator->dataSource = static fn() => new Collection();
        $this->generator->generator = $generator = "Custom generator";
        $this->assertSame($generator, $this->generator->generator);
        $this->generator->docs = $docs = "https://nexendrie.gitlab.io/rss";
        $this->assertSame($docs, $this->generator->docs);

        $result = new \SimpleXMLElement($this->generator->generate($info));
        $this->assertSame($info["language"]->value, (string) $result->channel->language);
        $this->assertSame($info["copyright"], (string) $result->channel->copyright);
        $this->assertSame($info["managingEditor"], (string) $result->channel->managingEditor);
        $this->assertSame($info["webMaster"], (string) $result->channel->webMaster);
        $this->assertSame($generator, (string) $result->channel->generator);
        $this->assertSame($docs, (string) $result->channel->docs);
        $this->assertSame((string) $info["ttl"], (string) $result->channel->ttl);
        $this->assertSame($info["rating"], (string) $result->channel->rating);
        $this->assertSame(SkipDay::Monday->name, (string) $result->channel->skipDays->day[0]);
        $this->assertSame(SkipDay::Sunday->name, (string) $result->channel->skipDays->day[1]);
        $this->assertSame("", (string) $result->channel->skipDays->day[2]);
        $this->assertSame((string) $info["skipHours"][0], (string) $result->channel->skipHours->hour[0]);
        $this->assertSame((string) $info["skipHours"][2], (string) $result->channel->skipHours->hour[1]);
        $this->assertSame("", (string) $result->channel->skipHours->hour[2]);
        $this->assertSame($info["image"]->url, (string) $result->channel->image->url);
        $this->assertSame($info["image"]->title, (string) $result->channel->image->title);
        $this->assertSame($info["image"]->description, (string) $result->channel->image->description);
        $this->assertSame($info["cloud"]->domain, (string) $result->channel->cloud["domain"]);
        $this->assertSame((string) $info["cloud"]->port, (string) $result->channel->cloud["port"]);
        $this->assertSame($info["cloud"]->path, (string) $result->channel->cloud["path"]);
        $this->assertSame($info["cloud"]->registerProcedure, (string) $result->channel->cloud["registerProcedure"]);
        $this->assertSame($info["cloud"]->protocol->value, (string) $result->channel->cloud["protocol"]);
        $this->assertSame($info["textInput"]->title, (string) $result->channel->textInput->title);
        $this->assertSame($info["textInput"]->name, (string) $result->channel->textInput->name);
        $this->assertSame($info["textInput"]->description, (string) $result->channel->textInput->description);
        $this->assertSame($info["textInput"]->link, (string) $result->channel->textInput->link);

        $this->generator->generator = "";
        $this->generator->docs = "";
        $info["language"] = Iso639Language::English;
        $result = new \SimpleXMLElement($this->generator->generate($info));
        $this->assertSame("", (string) $result->channel->generator);
        $this->assertSame("", (string) $result->channel->docs);
        $this->assertSame($info["language"]->value, (string) $result->channel->language);
    }

    public function testCategories(): void
    {
        $this->generator->dataSource = static fn() => new Collection();
        $info = [
            "title" => "Nexendrie RSS", "link" => "https://gitlab.com/nexendrie/rss/",
            "description" => "News for package nexendrie/rss", "categories" => [],
        ];
        $info["categories"][] = new Category("abc");
        $info["categories"][] = new Category("def", "domain");
        $result = new \SimpleXMLElement($this->generator->generate($info));
        $this->assertSame("abc", (string) $result->channel->category[0]);
        $this->assertSame("", (string) $result->channel->category[0]["domain"]);
        $this->assertSame("def", (string) $result->channel->category[1]);
        $this->assertSame("domain", (string) $result->channel->category[1]["domain"]);
    }

    public function testCustomTemplate(): void
    {
        $this->assertThrowsException(function () {
            $this->generator->template = "abc.xml";
        }, \RuntimeException::class);
        $templateFilename = __DIR__ . "/template.xml";
        $this->generator->template = $templateFilename;
        $this->assertSame($templateFilename, $this->generator->template);
        $info = [
            "title" => "Nexendrie RSS", "link" => "https://gitlab.com/nexendrie/rss/",
            "description" => "News for package nexendrie/rss",
        ];
        $this->generator->dataSource = static fn() => new Collection();
        $result = $this->generator->generate($info);
        $this->assertType("string", $result);
        $result = new \SimpleXMLElement($this->generator->generate($info));
        $this->assertSame("en", (string) $result->channel->language);
    }

    public function testExtension(): void
    {
        $this->generator->extensions[] = $extension = new TestExtension();
        $extensionName = $extension->getName();
        $extensionNamespace = $extension->getNamespace();
        $elementName = TestExtension::ELEMENT_ABC;
        $info = [
            "title" => "Nexendrie RSS", "link" => "https://gitlab.com/nexendrie/rss/",
            "description" => "News for package nexendrie/rss", "$extensionName:$elementName" => "def",
        ];
        $this->generator->dataSource = static fn() => new Collection();
        $result = $this->generator->generate($info);
        $this->assertType("string", $result);
        $result = new \SimpleXMLElement($result);
        $namespaces = $result->getNamespaces(true);
        $this->assertSame($extension->getNamespace(), $namespaces[$extensionName]);
        $this->assertSame(
            $info["$extensionName:$elementName"],
            (string) $result->channel->children($extensionNamespace, false)->$elementName
        );
    }

    public function testEvents(): void
    {
        $eventDispatcher = new DebugEventDispatcher(new DummyEventDispatcher(), new NullLogger());
        $generator = new Generator($eventDispatcher);
        $info = [
            "title" => "Nexendrie RSS", "link" => "https://gitlab.com/nexendrie/rss/",
            "description" => "News for package nexendrie/rss",
        ];
        $generator->dataSource = static function () {
            $items = new Collection();
            $items[] = new RssChannelItem([
                "title" => "Item 1", "description" => "Item 1 description", "link" => "https://example.com/item/1",
                "pubDate" => new DateTime(),
            ]);
            $items[] = new RssChannelItem([
                "title" => "Item 2", "description" => "Item 1 description", "link" => "https://example.com/item/1",
                "pubDate" => new DateTime(),
            ]);
            return $items;
        };
        $generator->generate($info);
        $this->assertTrue($eventDispatcher->dispatched(ChannelBeforeGenerate::class));
        $this->assertFalse($eventDispatcher->dispatched(ChannelBeforeGenerate::class, 2));
        $this->assertTrue($eventDispatcher->dispatched(ChannelAfterGenerate::class));
        $this->assertFalse($eventDispatcher->dispatched(ChannelAfterGenerate::class, 2));
        $this->assertTrue($eventDispatcher->dispatched(ItemAdded::class, 2));
        $this->assertFalse($eventDispatcher->dispatched(ItemAdded::class, 3));
    }
}
