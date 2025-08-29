<?php
declare(strict_types=1);

namespace Nexendrie\Rss;

use Nexendrie\Rss\Bridges\NetteApplication\RssResponse;
use Nexendrie\Rss\Extensions\RssCore\SkipDay;
use Tester\Assert;
use Nexendrie\Rss\Extensions\TestExtension;

require __DIR__ . "/../../bootstrap.php";

/**
 * @author Jakub Konečný
 * @testCase
 */
final class GeneratorTest extends \Tester\TestCase
{
    private Generator $generator;

    public function setUp(): void
    {
        $this->generator = new Generator();
    }

    protected function countItems(\SimpleXMLElement $channel): int
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
        $this->generator->dataSource = function () {
            return new Collection();
        };
        $result = $this->generator->generate($info);
        Assert::type("string", $result);
        $result = new \SimpleXMLElement($result);
        Assert::same("Test", (string) $result->channel->title);
        Assert::same("Test RSS Channel", (string) $result->channel->description);
        Assert::same("", (string) $result->channel->pubDate);
        Assert::same(0, $this->countItems($result));
    }

    public function testGenerate(): void
    {
        $info = [
            "title" => "Nexendrie RSS", "link" => "https://gitlab.com/nexendrie/rss/",
            "description" => "News for package nexendrie/rss",
        ];
        $this->generator->dataSource = function () {
            $items = new Collection();
            $items[] = new RssChannelItem([
                "title" => "Item 1", "description" => "Item 1 description", "link" => "", "pubDate" => 123
            ]);
            return $items;
        };
        $result = $this->generator->generate($info);
        Assert::type("string", $result);
        $result = new \SimpleXMLElement($result);
        Assert::same($info["title"], (string) $result->channel->title);
        Assert::same($info["description"], (string) $result->channel->description);
        Assert::same($info["link"], (string) $result->channel->link);
        Assert::same(1, $this->countItems($result));
        Assert::type("string", (string) $result->channel->lastBuidDate);
        Assert::same("", (string) $result->channel->pubDate);
    }

    public function testInvalidDataSource(): void
    {
        $this->generator->dataSource = function () {
            return [];
        };
        Assert::exception(function () {
            $this->generator->generate(["title" => "", "link" => "", "description" => "",]);
        }, \InvalidArgumentException::class);
    }

    public function testCustomLastBuildDate(): void
    {
        $info = [
            "title" => "Nexendrie RSS", "link" => "https://gitlab.com/nexendrie/rss/",
            "description" => "News for package nexendrie/rss", "lastBuildDate" => function () {
                return new \DateTime("2024-12-31");
            },
        ];
        $this->generator->dataSource = function () {
            $items = new Collection();
            $items[] = new RssChannelItem([
                "title" => "Item 1", "description" => "Item 1 description", "link" => "", "pubDate" => 123,
            ]);
            return $items;
        };
        $result = new \SimpleXMLElement($this->generator->generate($info));
        Assert::type("string", (string) $result->channel->lastBuidDate);
    }

    public function testInvalidLastBuildDate(): void
    {
        $this->generator->dataSource = function () {
            $items = new Collection();
            $items[] = new RssChannelItem([
                "title" => "Item 1", "description" => "Item 1 description", "link" => "", "pubDate" => 123,
            ]);
            return $items;
        };
        Assert::exception(function () {
            $info = [
                "title" => "Nexendrie RSS", "link" => "https://gitlab.com/nexendrie/rss/",
                "description" => "News for package nexendrie/rss", "lastBuildDate" => function () {
                    return "abc";
                },
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
        Assert::exception(function () use ($info) {
            $this->generator->response($info);
        }, InvalidStateException::class);
        $this->generator->dataSource = function () {
            $items = new Collection();
            $items[] = new RssChannelItem([
                "title" => "Item 1", "description" => "Item 1 description", "link" => "", "pubDate" => 123,
            ]);
            return $items;
        };
        $result = $this->generator->response($info);
        Assert::type(RssResponse::class, $result);
        Assert::type("string", $result->source);
    }

    public function testDateTimeFormat(): void
    {
        $info = [
            "title" => "Nexendrie RSS", "link" => "https://gitlab.com/nexendrie/rss/",
            "description" => "News for package nexendrie/rss",
        ];
        $dateTimeFormat = "Y/m/d";
        $this->generator->dateTimeFormat = $dateTimeFormat;
        Assert::same($dateTimeFormat, $this->generator->dateTimeFormat);
        $this->generator->dataSource = function () {
            return new Collection();
        };
        $result = new \SimpleXMLElement($this->generator->generate($info));
        Assert::same(date($dateTimeFormat), (string) $result->channel->lastBuildDate);
    }

    public function testPubDate(): void
    {
        $info = [
            "title" => "Nexendrie RSS", "link" => "https://gitlab.com/nexendrie/rss/",
            "description" => "News for package nexendrie/rss", "pubDate" => function () {
                return "abc";
            },
        ];
        $dateTimeFormat = "Y/m/d";
        $this->generator->dateTimeFormat = $dateTimeFormat;
        $this->generator->dataSource = function () {
            $items = new Collection();
            $items[] = new RssChannelItem([
                "title" => "Item 1", "description" => "Item 1 description", "link" => "", "pubDate" => 123,
            ]);
            return $items;
        };
        Assert::exception(function () use ($info) {
            $this->generator->generate($info);
        }, \InvalidArgumentException::class, "Callback for pub date for RSS generator has to return DateTime.");
        $info["pubDate"] = function () {
            return new \DateTime("2024-12-31");
        };
        $result = new \SimpleXMLElement($this->generator->generate($info));
        Assert::same("2024/12/31", (string) $result->channel->pubDate);
    }

    public function testOptionalThings(): void
    {
        $info = [
            "title" => "Nexendrie RSS", "link" => "https://gitlab.com/nexendrie/rss/", "language" => "en",
            "description" => "News for package nexendrie/rss", "copyright" => "Copyright 2019, Abc",
            "managingEditor" => "Abc", "webMaster" => "Def", "ttl" => 60,
            "rating" => "(PICS-1.1 \"http://www.classify.org/safesurf/\" 1 r (SS~~000 1))",
            "skipDays" => [SkipDay::Monday, SkipDay::Monday, SkipDay::Sunday,], "skipHours" => [1, 1, 10],
            "image" => new Image("url", "title", "description"),
            "cloud" => new Cloud("test.com", 80, "/test", "test.a", "http-post"),
            "textInput" => new TextInput("title", "description", "name", "link"),
        ];
        $this->generator->dataSource = function () {
            return new Collection();
        };
        $this->generator->generator = $generator = "Custom generator";
        Assert::same($generator, $this->generator->generator);
        $this->generator->docs = $docs = "https://nexendrie.gitlab.io/rss";
        Assert::same($docs, $this->generator->docs);

        $result = new \SimpleXMLElement($this->generator->generate($info));
        Assert::same($info["language"], (string) $result->channel->language);
        Assert::same($info["copyright"], (string) $result->channel->copyright);
        Assert::same($info["managingEditor"], (string) $result->channel->managingEditor);
        Assert::same($info["webMaster"], (string) $result->channel->webMaster);
        Assert::same($generator, (string) $result->channel->generator);
        Assert::same($docs, (string) $result->channel->docs);
        Assert::same((string) $info["ttl"], (string) $result->channel->ttl);
        Assert::same($info["rating"], (string) $result->channel->rating);
        Assert::same(SkipDay::Monday->name, (string) $result->channel->skipDays->day[0]);
        Assert::same(SkipDay::Sunday->name, (string) $result->channel->skipDays->day[1]);
        Assert::same("", (string) $result->channel->skipDays->day[2]);
        Assert::same((string) $info["skipHours"][0], (string) $result->channel->skipHours->hour[0]);
        Assert::same((string) $info["skipHours"][2], (string) $result->channel->skipHours->hour[1]);
        Assert::same("", (string) $result->channel->skipHours->hour[2]);
        Assert::same($info["image"]->url, (string) $result->channel->image->url);
        Assert::same($info["image"]->title, (string) $result->channel->image->title);
        Assert::same($info["image"]->description, (string) $result->channel->image->description);
        Assert::same($info["cloud"]->domain, (string) $result->channel->cloud["domain"]);
        Assert::same((string) $info["cloud"]->port, (string) $result->channel->cloud["port"]);
        Assert::same($info["cloud"]->path, (string) $result->channel->cloud["path"]);
        Assert::same($info["cloud"]->registerProcedure, (string) $result->channel->cloud["registerProcedure"]);
        Assert::same($info["cloud"]->protocol, (string) $result->channel->cloud["protocol"]);
        Assert::same($info["textInput"]->title, (string) $result->channel->textInput->title);
        Assert::same($info["textInput"]->name, (string) $result->channel->textInput->name);
        Assert::same($info["textInput"]->description, (string) $result->channel->textInput->description);
        Assert::same($info["textInput"]->link, (string) $result->channel->textInput->link);

        $this->generator->generator = "";
        $this->generator->docs = "";
        $result = new \SimpleXMLElement($this->generator->generate($info));
        Assert::same("", (string) $result->channel->generator);
        Assert::same("", (string) $result->channel->docs);
    }

    public function testCategories(): void
    {
        $this->generator->dataSource = function () {
            return new Collection();
        };
        $info = [
            "title" => "Nexendrie RSS", "link" => "https://gitlab.com/nexendrie/rss/",
            "description" => "News for package nexendrie/rss", "categories" => [],
        ];
        $info["categories"][] = new Category("abc");
        $info["categories"][] = new Category("def", "domain");
        $result = new \SimpleXMLElement($this->generator->generate($info));
        Assert::same("abc", (string) $result->channel->category[0]);
        Assert::same("", (string) $result->channel->category[0]["domain"]);
        Assert::same("def", (string) $result->channel->category[1]);
        Assert::same("domain", (string) $result->channel->category[1]["domain"]);
    }

    public function testCustomTemplate(): void
    {
        Assert::exception(function () {
            $this->generator->template = "abc.xml";
        }, \RuntimeException::class);
        $templateFilename = __DIR__ . "/template.xml";
        $this->generator->template = $templateFilename;
        Assert::same($templateFilename, $this->generator->template);
        $info = [
            "title" => "Nexendrie RSS", "link" => "https://gitlab.com/nexendrie/rss/",
            "description" => "News for package nexendrie/rss",
        ];
        $this->generator->dataSource = function () {
            return new Collection();
        };
        $result = $this->generator->generate($info);
        Assert::type("string", $result);
        $result = new \SimpleXMLElement($this->generator->generate($info));
        Assert::same("en", (string) $result->channel->language);
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
        $this->generator->dataSource = function () {
            return new Collection();
        };
        $result = $this->generator->generate($info);
        Assert::type("string", $result);
        $result = new \SimpleXMLElement($result);
        $namespaces = $result->getNamespaces(true);
        Assert::same($extension->getNamespace(), $namespaces[$extensionName]);
        Assert::same($info["$extensionName:$elementName"], (string) $result->channel->children($extensionNamespace, false)->$elementName);
    }
}

$test = new GeneratorTest();
$test->run();
