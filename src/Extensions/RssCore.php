<?php
declare(strict_types=1);

namespace Nexendrie\Rss\Extensions;

use Nette\Utils\Arrays;
use Nexendrie\Rss\CategoriesCollection;
use Nexendrie\Rss\Category;
use Nexendrie\Rss\Cloud;
use Nexendrie\Rss\EnclosuresCollection;
use Nexendrie\Rss\Generator;
use Nexendrie\Rss\GenericElement;
use Nexendrie\Rss\Image;
use Nexendrie\Rss\IRssExtension;
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
final class RssCore implements IRssExtension {
  use \Nette\SmartObject;

  public function getName(): string {
    return "";
  }

  public function getNamespace(): string {
    return "";
  }

  public function configureChannelOptions(OptionsResolver $resolver, Generator $generator): void {
    $resolver->setRequired(["title", "description", "link", "lastBuildDate", ]);
    $resolver->setAllowedTypes("title", "string");
    $resolver->setAllowedTypes("description", "string");
    $resolver->setAllowedTypes("link", "string");
    $resolver->setAllowedTypes("lastBuildDate", "callable");
    $resolver->setDefault("lastBuildDate", "time");
    $resolver->setNormalizer("lastBuildDate", function(Options $options, callable $value) use($generator) {
      $value = call_user_func($value);
      if(!is_int($value)) {
        throw new \InvalidArgumentException("Callback for last build date for RSS generator has to return integer.");
      }
      $value = date($generator->dateTimeFormat, $value);
      return new GenericElement("lastBuildDate", $value);
    });
    $resolver->setDefined([
      "language", "copyright", "managingEditor", "webMaster", "ttl", "pubDate", "rating", "categories", "skipDays",
      "skipHours", "image", "cloud", "textInput",
    ]);
    $resolver->setAllowedTypes("language", "string");
    $resolver->setAllowedTypes("copyright", "string");
    $resolver->setAllowedTypes("managingEditor", "string");
    $resolver->setAllowedTypes("webMaster", "string");
    $resolver->setAllowedTypes("ttl", "int");
    $resolver->setAllowedValues("ttl", function(int $value) {
      return ($value >= 0);
    });
    $resolver->setAllowedTypes("pubDate", "callable");
    $resolver->setNormalizer("pubDate", function(Options $options, callable $value) use($generator) {
      $value = call_user_func($value);
      if(!is_int($value)) {
        throw new \InvalidArgumentException("Callback for pub date for RSS generator has to return integer.");
      }
      $value = date($generator->dateTimeFormat, $value);
      return new GenericElement("pubDate", $value);
    });
    $resolver->setAllowedTypes("rating", "string");
    $resolver->setAllowedTypes("categories", Category::class . "[]");
    $resolver->setNormalizer("categories", function(Options $options, array $value) {
      return CategoriesCollection::fromArray($value);
    });
    $resolver->setAllowedTypes("skipDays", "string[]");
    $resolver->setAllowedValues("skipDays", function(array $value) {
      $allowedValues = ["Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday", "Sunday", ];
      return Arrays::every($value, function(string $value) use($allowedValues) {
        return in_array($value, $allowedValues, true);
      });
    });
    $resolver->setNormalizer("skipDays", function(Options $options, array $value) {
      return new SkipDaysCollection($value);
    });
    $resolver->setAllowedTypes("skipHours", "int[]");
    $resolver->setAllowedValues("skipHours", function(array $value) {
      return Arrays::every($value, function(int $value) {
        return Numbers::isInRange($value, 0, 23);
      });
    });
    $resolver->setNormalizer("skipHours", function(Options $options, array $value) {
      return new SkipHoursCollection($value);
    });
    $resolver->setAllowedTypes("image", Image::class);
    $resolver->setAllowedTypes("cloud", Cloud::class);
    $resolver->setAllowedTypes("textInput", TextInput::class);
  }

  public function configureItemOptions(OptionsResolver $resolver, Generator $generator): void {
    $resolver->setRequired(["title", "description", "link", ]);
    $resolver->setAllowedTypes("title", "string");
    $resolver->setAllowedTypes("description", "string");
    $resolver->setNormalizer("description", function(Options $options, string $value) use($generator) {
      return $this->shortenDescription($value, $generator->shortenDescription);
    });
    $resolver->setAllowedTypes("link", "string");
    $resolver->setDefined([
      "pubDate", "author", "comments", "guid", "source", "categories", "enclosures",
    ]);
    $resolver->setAllowedTypes("pubDate", "int");
    $resolver->setNormalizer("pubDate", function(Options $options, int $value) use($generator) {
      return date($generator->dateTimeFormat, $value);
    });
    $resolver->setAllowedTypes("author", "string");
    $resolver->setAllowedTypes("comments", "string");
    $resolver->setAllowedTypes("guid", "string");
    $resolver->setAllowedTypes("source", Source::class);
    $resolver->setAllowedTypes("categories", CategoriesCollection::class);
    $resolver->setAllowedTypes("enclosures", EnclosuresCollection::class);
  }

  protected function shortenDescription(string $description, int $maxLength): string {
    if($maxLength < 1) {
      return $description;
    }
    $originalDescription = $description;
    $description = substr($description, 0, $maxLength);
    if($description !== $originalDescription) {
      $description .= "...";
    }
    return $description;
  }
}
?>