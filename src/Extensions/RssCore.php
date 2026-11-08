<?php
declare(strict_types=1);

namespace Nexendrie\Rss\Extensions;

use Closure;
use DateTimeInterface;
use Nexendrie\Rss\CategoriesCollection;
use Nexendrie\Rss\Category;
use Nexendrie\Rss\CDataElement;
use Nexendrie\Rss\Cloud;
use Nexendrie\Rss\Enclosure;
use Nexendrie\Rss\Extensions\RssCore\Iso639Language;
use Nexendrie\Rss\Extensions\RssCore\RssLanguage;
use Nexendrie\Rss\Extensions\RssCore\SkipDay;
use Nexendrie\Rss\Generator;
use Nexendrie\Rss\XmlElement;
use Nexendrie\Rss\Guid;
use Nexendrie\Rss\Image;
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
final class RssCore extends BaseExtension
{
    public const string ELEMENT_CHANNEL_TITLE = "title";
    public const string ELEMENT_CHANNEL_DESCRIPTION = "description";
    public const string ELEMENT_CHANNEL_LINK = "link";
    public const string ELEMENT_CHANNEL_LAST_BUILD_DATE = "lastBuildDate";
    public const string ELEMENT_CHANNEL_LANGUAGE = "language";
    public const string ELEMENT_CHANNEL_COPYRIGHT = "copyright";
    public const string ELEMENT_CHANNEL_MANAGING_EDITOR = "managingEditor";
    public const string ELEMENT_CHANNEL_WEBMASTER = "webMaster";
    public const string ELEMENT_CHANNEL_TTL = "ttl";
    public const string ELEMENT_CHANNEL_PUB_DATE = "pubDate";
    public const string ELEMENT_CHANNEL_RATING = "rating";
    public const string ELEMENT_CHANNEL_CATEGORIES = "categories";
    public const string ELEMENT_CHANNEL_SKIP_DAYS = "skipDays";
    public const string ELEMENT_CHANNEL_SKIP_HOURS = "skipHours";
    public const string ELEMENT_CHANNEL_IMAGE = "image";
    public const string ELEMENT_CHANNEL_CLOUD = "cloud";
    public const string ELEMENT_CHANNEL_TEXT_INPUT = "textInput";
    public const string ELEMENT_CHANNEL_DOCS = "docs";
    public const string ELEMENT_CHANNEL_GENERATOR = "generator";

    public const string ELEMENT_ITEM_TITLE = "title";
    public const string ELEMENT_ITEM_DESCRIPTION = "description";
    public const string ELEMENT_ITEM_LINK = "link";
    public const string ELEMENT_ITEM_PUB_DATE = "pubDate";
    public const string ELEMENT_ITEM_AUTHOR = "author";
    public const string ELEMENT_ITEM_COMMENTS = "comments";
    public const string ELEMENT_ITEM_GUID = "guid";
    public const string ELEMENT_ITEM_SOURCE = "source";
    public const string ELEMENT_ITEM_CATEGORIES = "categories";
    public const string ELEMENT_ITEM_ENCLOSURE = "enclosure";

    public function getName(): string
    {
        return "";
    }

    public function getNamespace(): string
    {
        return "";
    }

    protected function getElementTypes(): array
    {
        return [
            self::ELEMENT_CHANNEL_TITLE => "string",
            self::ELEMENT_CHANNEL_DESCRIPTION => "string",
            self::ELEMENT_CHANNEL_LINK => "url",
            self::ELEMENT_CHANNEL_LAST_BUILD_DATE => Closure::class,
            self::ELEMENT_CHANNEL_LANGUAGE => [Iso639Language::class, RssLanguage::class,],
            self::ELEMENT_CHANNEL_COPYRIGHT => "string",
            self::ELEMENT_CHANNEL_MANAGING_EDITOR => "email",
            self::ELEMENT_CHANNEL_WEBMASTER => "email",
            self::ELEMENT_CHANNEL_TTL => "non-negative-int",
            self::ELEMENT_CHANNEL_PUB_DATE => Closure::class,
            self::ELEMENT_CHANNEL_RATING => "string",
            self::ELEMENT_CHANNEL_IMAGE => Image::class,
            self::ELEMENT_CHANNEL_CLOUD => Cloud::class,
            self::ELEMENT_CHANNEL_TEXT_INPUT => TextInput::class,
            self::ELEMENT_CHANNEL_DOCS => "url",
            self::ELEMENT_CHANNEL_GENERATOR => "string",
            self::ELEMENT_ITEM_AUTHOR => "email",
            self::ELEMENT_ITEM_COMMENTS => "url",
            self::ELEMENT_ITEM_GUID => ["string", Guid::class,],
            self::ELEMENT_ITEM_SOURCE => Source::class,
            self::ELEMENT_ITEM_ENCLOSURE => Enclosure::class,
        ];
    }

    protected function getRequiredElements(): array
    {
        return [
            self::ELEMENT_CHANNEL_TITLE,
            self::ELEMENT_CHANNEL_DESCRIPTION,
            self::ELEMENT_CHANNEL_LAST_BUILD_DATE,
        ];
    }

    public function configureChannelOptions(OptionsResolver $resolver, Generator $generator): void
    {
        $this->registerElements($resolver, "ELEMENT_CHANNEL_");
        $resolver->setRequired([self::ELEMENT_CHANNEL_LINK,]);
        $resolver->setNormalizer(
            $this->getElementName(self::ELEMENT_CHANNEL_DESCRIPTION),
            fn (Options $options, string $value) => new CDataElement($this->getElementName(self::ELEMENT_CHANNEL_DESCRIPTION), $value)
        );
        $resolver->setDefault($this->getElementName(self::ELEMENT_CHANNEL_LAST_BUILD_DATE), date_create(...));
        $resolver->setNormalizer(
            $this->getElementName(self::ELEMENT_CHANNEL_LAST_BUILD_DATE),
            function (Options $options, Closure $value) use ($generator): XmlElement {
                $value = $value();
                if (!$value instanceof DateTimeInterface) {
                    throw new \InvalidArgumentException(
                        "Callback for last build date for RSS generator has to return " . DateTimeInterface::class . "."
                    );
                }
                $value = $value->format($generator->dateTimeFormat);
                return new XmlElement($this->getElementName(self::ELEMENT_CHANNEL_LAST_BUILD_DATE), $value);
            }
        );
        $resolver->setNormalizer(
            $this->getElementName(self::ELEMENT_CHANNEL_PUB_DATE),
            function (Options $options, Closure $value) use ($generator): XmlElement {
                $value = $value();
                if (!$value instanceof DateTimeInterface) {
                    throw new \InvalidArgumentException(
                        "Callback for pub date for RSS generator has to return " . DateTimeInterface::class . "."
                    );
                }
                $value = $value->format($generator->dateTimeFormat);
                return new XmlElement($this->getElementName(self::ELEMENT_CHANNEL_PUB_DATE), $value);
            }
        );
        $resolver->setAllowedTypes($this->getElementName(self::ELEMENT_CHANNEL_CATEGORIES), Category::class . "[]");
        $resolver->setNormalizer(
            $this->getElementName(self::ELEMENT_CHANNEL_CATEGORIES),
            // @phpstan-ignore argument.type
            static fn(Options $options, array $value): CategoriesCollection => CategoriesCollection::fromArray($value)
        );
        $resolver->setAllowedTypes($this->getElementName(self::ELEMENT_CHANNEL_SKIP_DAYS), SkipDay::class . "[]");
        $resolver->setNormalizer($this->getElementName(self::ELEMENT_CHANNEL_SKIP_DAYS), static function (Options $options, array $value): SkipDaysCollection {
            /** @var SkipDay $item */
            foreach ($value as &$item) {
                $item = $item->name;
            }
            /** @var list<string> $value */
            return new SkipDaysCollection($value);
        });
        $resolver->setAllowedTypes($this->getElementName(self::ELEMENT_CHANNEL_SKIP_HOURS), "int[]");
        $resolver->setAllowedValues($this->getElementName(self::ELEMENT_CHANNEL_SKIP_HOURS), static function (array $value): bool {
            return array_all($value, static function (int $value): bool {
                return Numbers::isInRange($value, 0, 23);
            });
        });
        $resolver->setNormalizer(
            $this->getElementName(self::ELEMENT_CHANNEL_SKIP_HOURS),
            // @phpstan-ignore argument.type
            static fn (Options $options, array $value): SkipHoursCollection => new SkipHoursCollection($value)
        );
    }

    public function configureItemOptions(OptionsResolver $resolver, Generator $generator): void
    {
        $this->registerElements($resolver, "ELEMENT_ITEM_");
        $resolver->setNormalizer($this->getElementName(self::ELEMENT_ITEM_DESCRIPTION), function (Options $options, string $value) use ($generator) {
            return new CDataElement(
                $this->getElementName(self::ELEMENT_ITEM_DESCRIPTION),
                $this->shortenDescription($value, $generator->shortenDescription)
            );
        });
        $resolver->setAllowedTypes($this->getElementName(self::ELEMENT_ITEM_PUB_DATE), DateTimeInterface::class);
        $resolver->setNormalizer(
            $this->getElementName(self::ELEMENT_ITEM_PUB_DATE),
            function (Options $options, DateTimeInterface $value) use ($generator): XmlElement {
                $value = $value->format($generator->dateTimeFormat);
                return new XmlElement($this->getElementName(self::ELEMENT_ITEM_PUB_DATE), $value);
            }
        );
        $resolver->addNormalizer(
            $this->getElementName(self::ELEMENT_ITEM_GUID),
            static fn(Options $options, string|Guid $value) => $value instanceof Guid ? $value : new Guid($value)
        );
        $resolver->setAllowedTypes($this->getElementName(self::ELEMENT_ITEM_CATEGORIES), CategoriesCollection::class);
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
