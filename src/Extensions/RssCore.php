<?php
declare(strict_types=1);

namespace Nexendrie\Rss\Extensions;

use DateTime;
use Nexendrie\Rss\CategoriesCollection;
use Nexendrie\Rss\Category;
use Nexendrie\Rss\Cloud;
use Nexendrie\Rss\EnclosuresCollection;
use Nexendrie\Rss\Extensions\RssCore\Iso639Language;
use Nexendrie\Rss\Extensions\RssCore\RssLanguage;
use Nexendrie\Rss\Extensions\RssCore\SkipDay;
use Nexendrie\Rss\Generator;
use Nexendrie\Rss\GenericElement;
use Nexendrie\Rss\Guid;
use Nexendrie\Rss\Image;
use Nexendrie\Rss\RssExtension;
use Nexendrie\Rss\SkipDaysCollection;
use Nexendrie\Rss\SkipHoursCollection;
use Nexendrie\Rss\Source;
use Nexendrie\Rss\TextInput;
use Nexendrie\Utils\Numbers;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * RssCore
 *
 * @author Jakub Konečný
 */
final class RssCore implements RssExtension
{
    public function getName(): string
    {
        return "";
    }

    public function getNamespace(): string
    {
        return "";
    }

    public function configureChannelOptions(OptionsResolver $resolver, Generator $generator): void
    {
        $resolver->setRequired(["title", "description", "link", "lastBuildDate",]);
        $resolver->setAllowedTypes("title", "string");
        $resolver->setAllowedTypes("description", "string");
        $resolver->setAllowedTypes("link", "string");
        $resolver->setAllowedTypes("lastBuildDate", "callable");
        $resolver->setDefault("lastBuildDate", "date_create");
        $resolver->setNormalizer(
            "lastBuildDate",
            static function (Options $options, callable $value) use ($generator): GenericElement {
                $value = call_user_func($value);
                if (!$value instanceof DateTime) {
                    throw new \InvalidArgumentException(
                        "Callback for last build date for RSS generator has to return " . DateTime::class . "."
                    );
                }
                $value = $value->format($generator->dateTimeFormat);
                return new GenericElement("lastBuildDate", $value);
            }
        );
        $resolver->setDefined([
            "language", "copyright", "managingEditor", "webMaster", "ttl", "pubDate", "rating", "categories",
            "skipDays", "skipHours", "image", "cloud", "textInput",
        ]);
        $resolver->setAllowedTypes("language", [Iso639Language::class, RssLanguage::class,]);
        $resolver->setNormalizer(
            "language",
            static fn(Options $options, Iso639Language|RssLanguage $value) => $value->value
        );
        $resolver->setAllowedTypes("copyright", "string");
        $resolver->setAllowedTypes("managingEditor", "string");
        $resolver->setAllowedTypes("webMaster", "string");
        $resolver->setAllowedTypes("ttl", "int");
        $resolver->setAllowedValues("ttl", static fn(int $value): bool => ($value >= 0));
        $resolver->setAllowedTypes("pubDate", "callable");
        $resolver->setNormalizer(
            "pubDate",
            static function (Options $options, callable $value) use ($generator): GenericElement {
                $value = call_user_func($value);
                if (!$value instanceof DateTime) {
                    throw new \InvalidArgumentException(
                        "Callback for pub date for RSS generator has to return " . DateTime::class . "."
                    );
                }
                $value = $value->format($generator->dateTimeFormat);
                return new GenericElement("pubDate", $value);
            }
        );
        $resolver->setAllowedTypes("rating", "string");
        $resolver->setAllowedTypes("categories", Category::class . "[]");
        $resolver->setNormalizer(
            "categories",
            static fn(Options $options, array $value): CategoriesCollection => CategoriesCollection::fromArray($value)
        );
        $resolver->setAllowedTypes("skipDays", SkipDay::class . "[]");
        $resolver->setNormalizer("skipDays", static function (Options $options, array $value): SkipDaysCollection {
            /** @var SkipDay $item */
            foreach ($value as &$item) {
                $item = $item->name;
            }
            return new SkipDaysCollection($value);
        });
        $resolver->setAllowedTypes("skipHours", "int[]");
        $resolver->setAllowedValues("skipHours", static function (array $value): bool {
            return array_all($value, static function (int $value): bool {
                return Numbers::isInRange($value, 0, 23);
            });
        });
        $resolver->setNormalizer(
            "skipHours",
            static fn (Options $options, array $value): SkipHoursCollection => new SkipHoursCollection($value)
        );
        $resolver->setAllowedTypes("image", Image::class);
        $resolver->setAllowedTypes("cloud", Cloud::class);
        $resolver->setAllowedTypes("textInput", TextInput::class);
    }

    public function configureItemOptions(OptionsResolver $resolver, Generator $generator): void
    {
        $resolver->setRequired(["title", "description", "link",]);
        $resolver->setAllowedTypes("title", "string");
        $resolver->setAllowedTypes("description", "string");
        $resolver->setNormalizer("description", function (Options $options, string $value) use ($generator): string {
            return $this->shortenDescription($value, $generator->shortenDescription);
        });
        $resolver->setAllowedTypes("link", "string");
        $resolver->setDefined([
            "pubDate", "author", "comments", "guid", "source", "categories", "enclosures",
        ]);
        $resolver->setAllowedTypes("pubDate", DateTime::class);
        $resolver->setNormalizer(
            "pubDate",
            static function (Options $options, DateTime $value) use ($generator): GenericElement {
                $value = $value->format($generator->dateTimeFormat);
                return new GenericElement("pubDate", $value);
            }
        );
        $resolver->setAllowedTypes("author", "string");
        $resolver->setAllowedTypes("comments", "string");
        $resolver->setAllowedTypes("guid", ["string", Guid::class,]);
        $resolver->addNormalizer(
            "guid",
            static fn(Options $options, string|Guid $value) => $value instanceof Guid ? $value : new Guid($value)
        );
        $resolver->setAllowedTypes("source", Source::class);
        $resolver->setAllowedTypes("categories", CategoriesCollection::class);
        $resolver->setAllowedTypes("enclosures", EnclosuresCollection::class);
    }

    protected function shortenDescription(string $description, int $maxLength): string
    {
        if ($maxLength < 1) {
            return $description;
        }
        $originalDescription = $description;
        $description = substr($description, 0, $maxLength);
        if ($description !== $originalDescription) {
            $description .= "...";
        }
        return $description;
    }
}
