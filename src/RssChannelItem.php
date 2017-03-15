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
  
  /**
   * @param string $title
   * @param string $description
   * @param string $link
   * @param string $pubDate
   */
  function __construct(string $title, string $description, string $link, string $pubDate) {
    $this->title = $title;
    $this->description = $description;
    $this->link = $link;
    $this->pubDate = $pubDate;
  }
  
  /**
   * @return string
   */
  function getTitle(): string {
    return $this->title;
  }
  
  /**
   * @param string $title
   */
  function setTitle(string $title) {
    $this->title = $title;
  }
  
  /**
   * @return string
   */
  function getDescription(): string {
    return $this->description;
  }
  
  /**
   * @param string $description
   */
  function setDescription(string $description) {
    $this->description = $description;
  }
  
  /**
   * @return string
   */
  function getLink(): string {
    return $this->link;
  }
  
  /**
   * @param string $link
   */
  function setLink(string $link) {
    $this->link = $link;
  }
  
  /**
   * @return string
   */
  function getPubDate(): string {
    return $this->pubDate;
  }
  
  /**
   * @param string $pubDate
   */
  function setPubDate(string $pubDate) {
    $this->pubDate = $pubDate;
  }
}
?>