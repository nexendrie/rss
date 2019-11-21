<?php
declare(strict_types=1);

namespace Nexendrie\Rss;

use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Rss Channel Item
 *
 * @author Jakub Konečný
 */
class RssChannelItem {
  use \Nette\SmartObject;

  /** @var array */
  protected $data;
  
  public function __construct(array $data) {
    $this->data = $data;
  }

  protected function configureOptions(OptionsResolver $resolver, Generator $generator): void {
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

  public function toXml(\SimpleXMLElement &$element, Generator $generator): void {
    $resolver = new OptionsResolver();
    $this->configureOptions($resolver, $generator);
    $data = $resolver->resolve($this->data);
    foreach($data as $key => $value) {
      if($value === "") {
        continue;
      }
      if($value instanceof IXmlConvertible) {
        $value->appendToXml($element);
      } else {
        $element->addChild($key, $value);
      }
    }
  }
}
?>