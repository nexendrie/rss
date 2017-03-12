<?php
declare(strict_types=1);

namespace Nexendrie\Rss;

/**
 * RSS Channel Generator
 *
 * @author Jakub Konečný
 * @property callable $dataSource
 * @property int $shortenDescription
 * @property string $dateTimeFormat
 * @property callable|int $lastBuildDate
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
  protected $dateTimeFormat = "Y-m-d H:i:s";
  /** @var callable|null */
  protected $dataSource = NULL;
  /** @var int */
  protected $shortenDescription = 150;
  /** @var callable|int */
  protected $lastBuildDate = "time";
  
  /**
   * @param callable $dataSource
   */
  function setDataSource(callable $dataSource) {
    $this->dataSource = $dataSource;
  }
  
  /**
   * @return int
   */
  function getShortenDescription(): int {
    return $this->shortenDescription;
  }
  
  /**
   * @param int $value
   */
  function setShortenDescription(int $value) {
    $this->shortenDescription = $value;
  }
  
  /**
   * @return string
   */
  function getDateTimeFormat(): string {
    return $this->dateTimeFormat;
  }
  
  /**
   * @param string $format
   */
  function setDateTimeFormat(string $format) {
    $this->dateTimeFormat = $format;
  }
  
  /**
   * @return callable|int
   */
  function getLastBuildDate() {
    return $this->lastBuildDate;
  }
  
  /**
   * @param callable|int $lastBuildDate
   */
  function setLastBuildDate($lastBuildDate) {
    $this->lastBuildDate = $lastBuildDate;
  }
  
  /**
   * @return \SimpleXMLElement
   * @throws InvalidStateException
   * @throws \InvalidArgumentException
   */
  function generate(): \SimpleXMLElement {
    if(is_null($this->dataSource)) {
      throw new InvalidStateException("Data source for RSS generator is not set.");
    }
    $items = call_user_func($this->dataSource);
    $channel = simplexml_load_file(__DIR__ . "/template.xml");
    if($this->link) {
      $channel->channel->link[0][0] = $this->link;
    }
    if(is_callable($this->lastBuildDate)) {
      $lastBuildDate = call_user_func($this->lastBuildDate);
      if(!is_int($lastBuildDate)) {
        throw new \InvalidArgumentException("Callback for last build date for RSS generator has to return integer.");
      }
    } elseif(is_int($this->lastBuildDate)) {
      $lastBuildDate = $this->lastBuildDate;
    } else {
      throw new \InvalidArgumentException("Last build date for RSS generator has to be callback or integer.");
    }
    $channel->channel->lastBuildDate[0][0] = date($this->dateTimeFormat, $lastBuildDate);
    if($this->title) {
      $channel->channel->title[0][0] = $this->title;
    }
    if($this->description) {
      $channel->channel->description[0][0] = $this->description;
    }
    /** @var RssChannelItem $item */
    foreach($items as $item) {
      if(!$item instanceof RssChannelItem) {
        throw new \InvalidArgumentException("The item is not of type " . RssChannelItem::class);
      }
      /** @var \SimpleXMLElement $i */
      $i = $channel->channel->addChild("item");
      $i->addChild("title", $item->title);
      $i->addChild("link", $item->link);
      $i->addChild("pubDate", $item->pubDate);
      $description = ($this->shortenDescription) ? substr($item->description, 0, $this->shortenDescription) : $item->description;
      if($description !== $item->description) {
        $description .= "...";
      }
      $i->addChild("description", $description);
    }
    return $channel;
  }
  
  /**
   * @return RssResponse
   * @throws \Exception
   */
  function response(): RssResponse {
    try {
      return new RssResponse($this->generate());
    } catch(\Exception $e) {
      throw $e;
    }
  }
}
?>