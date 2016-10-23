<?php
namespace Nexendrie\Rss;

/**
 * RSS Channel Generator
 *
 * @author Jakub Konečný
 * @property callable $dataSource
 * @property int $shortenDescription
 */
class Generator {
  use \Nette\SmartObject;
  
  /** @var string */
  public $title = "";
  /** @var string */
  public $description = "";
  /** @var string */
  public $link = "";
  /** @var string */
  public $dateTimeFormat = "Y-m-d H:i:s";
  /** @var callable */
  protected $dataSource = NULL;
  /** @var int */
  protected $shortenDescription = 150;
  
  /**
   * @param callable $dataSource
   */
  function setDataSource(callable $dataSource) {
    $this->dataSource = $dataSource;
  }
  
  /**
   * @return int
   */
  function getShortenDescription() {
    return $this->shortenDescription;
  }
  
  /**
   * @param int $value
   */
  function setShortenDescription($value) {
    $this->shortenDescription = (int) $value;
  }
  
  /**
   * @return \SimpleXMLElement
   * @throws \Exception
   */
  function generate() {
    if(is_null($this->dataSource)) throw new \Exception("Data source for RSS generator is not set.");
    $items = call_user_func($this->dataSource);
    $channel = simplexml_load_file(__DIR__ . "/template.xml");
    unset($channel->channel->link);
    unset($channel->channel->lastBuildDate);
    if($this->link) $channel->channel->addChild("link", $this->link);
    $channel->channel->addChild("lastBuildDate", date($this->dateTimeFormat));
    if($this->title) {
      unset($channel->channel->title);
      $channel->channel->addChild("title", $this->title);
    }
    if($this->description) {
      unset($channel->channel->description);
      $channel->channel->addChild("description", $this->description);
    }
    /** @var RssChannelItem $item */
    foreach($items as $item) {
      if(!$item instanceof RssChannelItem) throw new \Exception();
      /** @var \SimpleXMLElement $i */
      $i = $channel->channel->addChild("item");
      $i->addChild("title", $item->title);
      $i->addChild("link", $item->link);
      $i->addChild("pubDate", (string) $item->pubDate);
      $i->addChild("description", ($this->shortenDescription)? substr($item->description, 0, $this->shortenDescription): $item->description);
    }
    return $channel;
  }
}
?>