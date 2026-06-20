<?php
declare(strict_types=1);

namespace Nexendrie\Rss\Extensions;

use DateTime;
use MyTester\Attributes\Group;
use MyTester\Attributes\TestSuite;
use Nexendrie\Rss\Collection;
use Nexendrie\Rss\Extensions\RssCore\RssLanguage;
use Nexendrie\Rss\Extensions\RssCore\SkipDay;
use Nexendrie\Rss\Generator;
use Nexendrie\Rss\RssChannelItem;
use ReflectionMethod;
use Symfony\Component\OptionsResolver\Exception\InvalidOptionsException;
use Symfony\Component\OptionsResolver\Exception\MissingOptionsException;
use Symfony\Component\OptionsResolver\OptionsResolver;

#[TestSuite("BaseExtension")]
#[Group("extensions")]
final class BaseExtensionTest extends \MyTester\TestCase
{
    public function testGetName(): void
    {
        $this->assertSame("content", (new Content())->getName());
        $this->assertSame("creativeCommons", (new CreativeCommons())->getName());
    }

    public function testGetElementName(): void
    {
        $extension = new class extends BaseExtension {
            public function getName(): string
            {
                return "test";
            }

            public function getNamespace(): string
            {
                return "https://example.com/rss/test/";
            }
        };
        $rm = new ReflectionMethod(BaseExtension::class, "getElementName");
        $result = $rm->invoke($extension, "abc");
        $this->assertSame("test:abc", $result);
    }

    public function testSimpleTypeElement(): void
    {
        $extension = new class extends BaseExtension {
            public const string ELEMENT_ABC = "abc";

            public function getName(): string
            {
                return "test";
            }

            public function getNamespace(): string
            {
                return "https://example.com/rss/test/";
            }

            protected function getElementTypes(): array
            {
                return [
                    self::ELEMENT_ABC => "string",
                ];
            }

            public function configureItemOptions(OptionsResolver $resolver, Generator $generator): void
            {
                $this->registerElements($resolver);
            }
        };
        $generator = new Generator();
        $generator->extensions[] = $extension;
        $info = [
            "title" => "Nexendrie RSS", "link" => "https://gitlab.com/nexendrie/rss/",
            "description" => "News for package nexendrie/rss",
        ];
        $generator->dataSource = static function () {
            $collection = new Collection();
            $collection[] = new RssChannelItem([
                "title" => "Item 1", "description" => "Item 1 description", "link" => "https://example.com/item/1",
                "pubDate" => new DateTime(), "test:abc" => "def",
            ]);
            return $collection;
        };
        $result = $generator->generate($info);
        $this->assertType("string", $result);
        $result = new \SimpleXMLElement($result);
        $namespaces = $result->getNamespaces(true);
        $this->assertSame($extension->getNamespace(), $namespaces[$extension->getName()]);
        $this->assertSame("def", (string) $result->channel->item->children($extension->getNamespace())->abc);

        $this->assertThrowsException(
            static function () use ($generator, $info) {
                $generator->dataSource = static function () {
                    $collection = new Collection();
                    $collection[] = new RssChannelItem([
                        "title" => "Item 1", "description" => "Item 1 description",
                        "link" => "https://example.com/item/1", "pubDate" => new DateTime(), "test:abc" => 123,
                    ]);
                    return $collection;
                };
                $generator->generate($info);
            },
            InvalidOptionsException::class,
            'The option "test:abc" with value 123 is expected to be of type "string", but is of type "int".'
        );
    }

    public function testEnumElement(): void
    {
        $extension = new class extends BaseExtension {
            public const string ELEMENT_ABC = "abc";

            public function getName(): string
            {
                return "test";
            }

            public function getNamespace(): string
            {
                return "https://example.com/rss/test/";
            }

            protected function getElementTypes(): array
            {
                return [
                    self::ELEMENT_ABC => SkipDay::class,
                ];
            }

            public function configureItemOptions(OptionsResolver $resolver, Generator $generator): void
            {
                $this->registerElements($resolver);
            }
        };
        $generator = new Generator();
        $generator->extensions[] = $extension;
        $info = [
            "title" => "Nexendrie RSS", "link" => "https://gitlab.com/nexendrie/rss/",
            "description" => "News for package nexendrie/rss",
        ];
        $generator->dataSource = static function () {
            $collection = new Collection();
            $collection[] = new RssChannelItem([
                "title" => "Item 1", "description" => "Item 1 description", "link" => "https://example.com/item/1",
                "pubDate" => new DateTime(), "test:abc" => SkipDay::Friday,
            ]);
            return $collection;
        };
        $result = $generator->generate($info);
        $this->assertType("string", $result);
        $result = new \SimpleXMLElement($result);
        $namespaces = $result->getNamespaces(true);
        $this->assertSame($extension->getNamespace(), $namespaces[$extension->getName()]);
        $this->assertSame("Friday", (string) $result->channel->item->children($extension->getNamespace())->abc);

        $extension = new class extends BaseExtension {
            public const string ELEMENT_ABC = "abc";

            public function getName(): string
            {
                return "test";
            }

            public function getNamespace(): string
            {
                return "https://example.com/rss/test/";
            }

            protected function getElementTypes(): array
            {
                return [
                    self::ELEMENT_ABC => RssLanguage::class,
                ];
            }

            public function configureItemOptions(OptionsResolver $resolver, Generator $generator): void
            {
                $this->registerElements($resolver);
            }
        };
        $generator = new Generator();
        $generator->extensions[] = $extension;
        $generator->dataSource = static function () {
            $collection = new Collection();
            $collection[] = new RssChannelItem([
                "title" => "Item 1", "description" => "Item 1 description", "link" => "https://example.com/item/1",
                "pubDate" => new DateTime(), "test:abc" => RssLanguage::English,
            ]);
            return $collection;
        };
        $result = $generator->generate($info);
        $this->assertType("string", $result);
        $result = new \SimpleXMLElement($result);
        $namespaces = $result->getNamespaces(true);
        $this->assertSame($extension->getNamespace(), $namespaces[$extension->getName()]);
        $this->assertSame("en", (string) $result->channel->item->children($extension->getNamespace())->abc);
    }

    public function testSpecialTypeElement(): void
    {
        $extension = new class extends BaseExtension {
            public const string ELEMENT_ABC = "abc";

            public function getName(): string
            {
                return "test";
            }

            public function getNamespace(): string
            {
                return "https://example.com/rss/test/";
            }

            protected function getElementTypes(): array
            {
                return [
                    self::ELEMENT_ABC => "positive-int",
                ];
            }

            public function configureItemOptions(OptionsResolver $resolver, Generator $generator): void
            {
                $this->registerElements($resolver);
            }
        };
        $generator = new Generator();
        $generator->extensions[] = $extension;
        $info = [
            "title" => "Nexendrie RSS", "link" => "https://gitlab.com/nexendrie/rss/",
            "description" => "News for package nexendrie/rss",
        ];
        $generator->dataSource = static function () {
            $collection = new Collection();
            $collection[] = new RssChannelItem([
                "title" => "Item 1", "description" => "Item 1 description", "link" => "https://example.com/item/1",
                "pubDate" => new DateTime(), "test:abc" => 1,
            ]);
            return $collection;
        };
        $result = $generator->generate($info);
        $this->assertType("string", $result);
        $result = new \SimpleXMLElement($result);
        $namespaces = $result->getNamespaces(true);
        $this->assertSame($extension->getNamespace(), $namespaces[$extension->getName()]);
        $this->assertSame("1", (string) $result->channel->item->children($extension->getNamespace())->abc);

        $this->assertThrowsException(
            static function () use ($generator, $info) {
                $generator->dataSource = static function () {
                    $collection = new Collection();
                    $collection[] = new RssChannelItem([
                        "title" => "Item 1", "description" => "Item 1 description",
                        "link" => "https://example.com/item/1", "pubDate" => new DateTime(), "test:abc" => "abc",
                    ]);
                    return $collection;
                };
                $generator->generate($info);
            },
            InvalidOptionsException::class,
            'The option "test:abc" with value "abc" is expected to be of type "int", but is of type "string".'
        );

        $this->assertThrowsException(
            static function () use ($generator, $info) {
                $generator->dataSource = static function () {
                    $collection = new Collection();
                    $collection[] = new RssChannelItem([
                        "title" => "Item 1", "description" => "Item 1 description",
                        "link" => "https://example.com/item/1", "pubDate" => new DateTime(), "test:abc" => -1,
                    ]);
                    return $collection;
                };
                $generator->generate($info);
            },
            InvalidOptionsException::class,
            'The option "test:abc" with value -1 is invalid.'
        );
    }

    public function testRequiredElement(): void
    {
        $extension = new class extends BaseExtension {
            public const string ELEMENT_ABC = "abc";

            public function getName(): string
            {
                return "test";
            }

            public function getNamespace(): string
            {
                return "https://example.com/rss/test/";
            }

            protected function getElementTypes(): array
            {
                return [
                    self::ELEMENT_ABC => "string",
                ];
            }

            protected function getRequiredElements(): array
            {
                return [
                    self::ELEMENT_ABC,
                ];
            }

            public function configureItemOptions(OptionsResolver $resolver, Generator $generator): void
            {
                $this->registerElements($resolver);
            }
        };
        $generator = new Generator();
        $generator->extensions[] = $extension;
        $info = [
            "title" => "Nexendrie RSS", "link" => "https://gitlab.com/nexendrie/rss/",
            "description" => "News for package nexendrie/rss",
        ];
        $generator->dataSource = static function () {
            $collection = new Collection();
            $collection[] = new RssChannelItem([
                "title" => "Item 1", "description" => "Item 1 description", "link" => "https://example.com/item/1",
                "pubDate" => new DateTime(), "test:abc" => "def",
            ]);
            return $collection;
        };
        $result = $generator->generate($info);
        $this->assertType("string", $result);
        $result = new \SimpleXMLElement($result);
        $namespaces = $result->getNamespaces(true);
        $this->assertSame($extension->getNamespace(), $namespaces[$extension->getName()]);
        $this->assertSame("def", (string) $result->channel->item->children($extension->getNamespace())->abc);

        $this->assertThrowsException(
            static function () use ($generator, $info) {
                $generator->dataSource = static function () {
                    $collection = new Collection();
                    $collection[] = new RssChannelItem([
                        "title" => "Item 1", "description" => "Item 1 description",
                        "link" => "https://example.com/item/1", "pubDate" => new DateTime(),
                    ]);
                    return $collection;
                };
                $generator->generate($info);
            },
            MissingOptionsException::class,
            'The required option "test:abc" is missing.'
        );
    }
}
