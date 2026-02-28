<?php
declare(strict_types=1);

namespace Nexendrie\Rss\Extensions;

use DateTime;
use Nexendrie\Rss\Collection;
use Nexendrie\Rss\Extensions\RssCore\RssLanguage;
use Nexendrie\Rss\Extensions\RssCore\SkipDay;
use Nexendrie\Rss\Generator;
use Nexendrie\Rss\RssChannelItem;
use ReflectionMethod;
use Symfony\Component\OptionsResolver\Exception\InvalidOptionsException;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Tester\Assert;

require __DIR__ . "/../../../bootstrap.php";

/**
 * @author Jakub Konečný
 * @testCase
 */
final class BaseExtensionTest extends \Tester\TestCase
{
    public function testGetName(): void
    {
        Assert::same("content", (new Content())->getName());
        Assert::same("creativeCommons", (new CreativeCommons())->getName());
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
        Assert::same("test:abc", $result);
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
                "title" => "Item 1", "description" => "Item 1 description", "link" => "", "pubDate" => new DateTime(),
                "test:abc" => "def",
            ]);
            return $collection;
        };
        $result = $generator->generate($info);
        Assert::type("string", $result);
        $result = new \SimpleXMLElement($result);
        $namespaces = $result->getNamespaces(true);
        Assert::same($extension->getNamespace(), $namespaces[$extension->getName()]);
        Assert::same("def", (string) $result->channel->item->children($extension->getNamespace())->abc);

        Assert::exception(
            static function () use ($generator, $info) {
                $generator->dataSource = static function () {
                    $collection = new Collection();
                    $collection[] = new RssChannelItem([
                        "title" => "Item 1", "description" => "Item 1 description", "link" => "",
                        "pubDate" => new DateTime(),
                        "test:abc" => 123,
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
                "title" => "Item 1", "description" => "Item 1 description", "link" => "", "pubDate" => new DateTime(),
                "test:abc" => SkipDay::Friday,
            ]);
            return $collection;
        };
        $result = $generator->generate($info);
        Assert::type("string", $result);
        $result = new \SimpleXMLElement($result);
        $namespaces = $result->getNamespaces(true);
        Assert::same($extension->getNamespace(), $namespaces[$extension->getName()]);
        Assert::same("Friday", (string) $result->channel->item->children($extension->getNamespace())->abc);

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
                "title" => "Item 1", "description" => "Item 1 description", "link" => "", "pubDate" => new DateTime(),
                "test:abc" => RssLanguage::English,
            ]);
            return $collection;
        };
        $result = $generator->generate($info);
        Assert::type("string", $result);
        $result = new \SimpleXMLElement($result);
        $namespaces = $result->getNamespaces(true);
        Assert::same($extension->getNamespace(), $namespaces[$extension->getName()]);
        Assert::same("en", (string) $result->channel->item->children($extension->getNamespace())->abc);
    }
}

$test = new BaseExtensionTest();
$test->run();
