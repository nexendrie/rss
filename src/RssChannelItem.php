<?php
declare(strict_types=1);

namespace Nexendrie\Rss;

/**
 * Rss Channel Item
 *
 * @author Jakub Konečný
 * @property string $title
 * @property string $description
 * @property string $link
 * @property int $pubDate
 * @property string $author
 * @property string $comments
 * @property string $guid
 * @property string $sourceUrl
 * @property string $sourceTitle
 * @property \Nexendrie\Utils\Collection|Category[] $categories
 * @property \Nexendrie\Utils\Collection|Enclosure[] $enclosures
 */
class RssChannelItem {
  use \Nette\SmartObject;
  
  /** @var string */
  protected $title;
  /** @var string */
  protected $description;
  /** @var string */
  protected $link;
  /** @var int */
  protected $pubDate;
  /** @var string */
  protected $author = "";
  /** @var string */
  protected $comments = "";
  /** @var string */
  protected $guid = "";
  /** @var \stdClass */
  protected $source;
  /** @var CategoriesCollection|Category[] */
  protected $categories;
  /** @var \Nexendrie\Utils\Collection|Enclosure[] */
  protected $enclosures;
  
  public function __construct(string $title, string $description, string $link, int $pubDate) {
    $this->title = $title;
    $this->description = $description;
    $this->link = $link;
    $this->pubDate = $pubDate;
    $this->categories = new CategoriesCollection();
    $this->enclosures = new class extends \Nexendrie\Utils\Collection implements IXmlConvertible {
      /** @var string */
      protected $class = Enclosure::class;

      public function appendToXml(\SimpleXMLElement &$parent): void {
        array_walk($this->items, function(Enclosure $value) use($parent) {
          $value->appendToXml($parent);
        });
      }
    };
    $this->source = new class extends \stdClass implements IXmlConvertible {
      /** @var string */
      public $url = "";
      /** @var string */
      public $title = "";

      public function appendToXml(\SimpleXMLElement &$parent): void {
        if($this->url !== "") {
          $element = $parent->addChild("source", $this->title);
          $element->addAttribute("url", $this->url);
        }
      }
    };
  }
  
  public function getTitle(): string {
    return $this->title;
  }
  
  public function setTitle(string $title): void {
    $this->title = $title;
  }
  
  public function getDescription(): string {
    return $this->description;
  }
  
  public function setDescription(string $description): void {
    $this->description = $description;
  }
  
  public function getLink(): string {
    return $this->link;
  }
  
  public function setLink(string $link): void {
    $this->link = $link;
  }
  
  public function getPubDate(): int {
    return $this->pubDate;
  }
  
  public function setPubDate(int $pubDate): void {
    $this->pubDate = $pubDate;
  }

  public function getAuthor(): string {
    return $this->author;
  }

  public function setAuthor(string $author): void {
    $this->author = $author;
  }

  public function getComments(): string {
    return $this->comments;
  }

  public function setComments(string $comments): void {
    $this->comments = $comments;
  }

  public function getGuid(): string {
    return $this->guid;
  }

  public function setGuid(string $guid): void {
    $this->guid = $guid;
  }

  public function getSourceUrl(): string {
    return $this->source->url;
  }

  public function setSourceUrl(string $sourceUrl): void {
    $this->source->url = $sourceUrl;
  }

  public function getSourceTitle(): string {
    return $this->source->title;
  }

  public function setSourceTitle(string $sourceTitle): void {
    $this->source->title = $sourceTitle;
  }

  /**
   * @return \Nexendrie\Utils\Collection|Category[]
   */
  public function getCategories(): \Nexendrie\Utils\Collection {
    return $this->categories;
  }

  /**
   * @return \Nexendrie\Utils\Collection|Enclosure[]
   */
  public function getEnclosures(): \Nexendrie\Utils\Collection {
    return $this->enclosures;
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

  /**
   * @param mixed $value
   * @return mixed
   */
  protected function normalizeValue(string $name, $value, Generator $generator) {
    switch($name) {
      case "pubDate":
        return date($generator->dateTimeFormat, $value);
      case "description":
        return $this->shortenDescription($value, $generator->shortenDescription);
      default:
        return $value;
    }
  }

  public function toXml(\SimpleXMLElement &$element, Generator $generator): void {
    $properties = get_object_vars($this);
    foreach($properties as $property => $value) {
      if($value === "") {
        continue;
      }
      $value = $this->normalizeValue($property, $value, $generator);
      if($value instanceof IXmlConvertible) {
        $value->appendToXml($element);
      } else {
        $element->addChild($property, $value);
      }
    }
  }
}
?>