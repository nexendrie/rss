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
 * @property \Nexendrie\Utils\Collection|Category[] $categories
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
  /** @var \Nexendrie\Utils\Collection|Category[] */
  protected $categories;
  
  public function __construct(string $title, string $description, string $link, int $pubDate) {
    $this->title = $title;
    $this->description = $description;
    $this->link = $link;
    $this->pubDate = $pubDate;
    $this->categories = new class extends \Nexendrie\Utils\Collection implements IXmlConvertible {
      /** @var string */
      protected $class = Category::class;

      public function appendToXml(\SimpleXMLElement &$parent): void {
        array_walk($this->items, function(Category $value) use($parent) {
          $value->appendToXml($parent);
        });
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

  /**
   * @return \Nexendrie\Utils\Collection|Category[]
   */
  public function getCategories(): \Nexendrie\Utils\Collection {
    return $this->categories;
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
    $properties = array_keys(get_object_vars($this));
    foreach($properties as $property) {
      if($this->$property === "") {
        continue;
      }
      $value = $this->normalizeValue($property, $this->$property, $generator);
      if($value instanceof IXmlConvertible) {
        $value->appendToXml($element);
      } else {
        $element->addChild($property, $value);
      }
    }
  }
}
?>