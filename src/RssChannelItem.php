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
 * @property string $pubDate
 */
class RssChannelItem {
  use \Nette\SmartObject;
  
  /** @var string */
  protected $title;
  /** @var string */
  protected $description;
  /** @var string */
  protected $link;
  /** @var string */
  protected $pubDate;
  
  public function __construct(string $title, string $description, string $link, string $pubDate) {
    $this->title = $title;
    $this->description = $description;
    $this->link = $link;
    $this->pubDate = $pubDate;
  }
  
  public function getTitle(): string {
    return $this->title;
  }
  
  public function setTitle(string $title) {
    $this->title = $title;
  }
  
  public function getDescription(): string {
    return $this->description;
  }
  
  public function setDescription(string $description) {
    $this->description = $description;
  }
  
  public function getLink(): string {
    return $this->link;
  }
  
  public function setLink(string $link) {
    $this->link = $link;
  }
  
  public function getPubDate(): string {
    return $this->pubDate;
  }
  
  public function setPubDate(string $pubDate) {
    $this->pubDate = $pubDate;
  }
}
?>