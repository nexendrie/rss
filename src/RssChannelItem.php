<?php
namespace Nexendrie\Rss;

/**
 * Rss Channel Item
 *
 * @author Jakub Konečný
 */
class RssChannelItem {
  use \Nette\SmartObject;
  
  /** @var string */
  public $title;
  /** @var string */
  public $description;
  /** @var string */
  public $link;
  /** @var string */
  public $pubDate;
  
  /**
   * @param string $title
   * @param string $description
   * @param string $link
   * @param string $pubDate
   */
  function __construct($title, $description, $link, $pubDate) {
    $this->title = $title;
    $this->description = $description;
    $this->link = $link;
    $this->pubDate = $pubDate;
  }
}
?>