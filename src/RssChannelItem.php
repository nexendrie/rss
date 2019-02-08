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
    $this->categories = new class extends \Nexendrie\Utils\Collection {
      /** @var string */
      protected $class = Category::class;
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
}
?>