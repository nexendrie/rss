<?php
declare(strict_types=1);

namespace Nexendrie\Rss;

/**
 * RSS Channel Generator
 *
 * @author Jakub Konečný
 * @property string $title
 * @property string $description
 * @property string $link
 * @property callable $dataSource
 * @property int $shortenDescription
 * @property string $dateTimeFormat
 * @property callable $lastBuildDate
 */
class Generator {
  use \Nette\SmartObject;
  
  /** @var string */
  protected $title = "";
  /** @var string */
  protected $description = "";
  /** @var string */
  protected $link = "";
  /** @var string */
  protected $dateTimeFormat = "Y-m-d H:i:s";
  /** @var callable|null */
  protected $dataSource = NULL;
  /** @var int */
  protected $shortenDescription = 150;
  /** @var callable */
  protected $lastBuildDate = "time";
  
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
   * @return callable
   */
  function getLastBuildDate() {
    return $this->lastBuildDate;
  }
  
  /**
   * @param callable $lastBuildDate
   */
  function setLastBuildDate(callable $lastBuildDate) {
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
    if(!$items instanceof Collection) {
      throw new \InvalidArgumentException("Callback for data source for RSS generator has to return " . Collection::class . ".");
    }
    $channel = simplexml_load_file(__DIR__ . "/template.xml");
    if($this->link) {
      $channel->channel->link[0][0] = $this->link;
    }
    $lastBuildDate = call_user_func($this->lastBuildDate);
    if(!is_int($lastBuildDate)) {
      throw new \InvalidArgumentException("Callback for last build date for RSS generator has to return integer.");
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
   * @throws InvalidStateException
   * @throws \InvalidArgumentException
   */
  function response(): RssResponse {
    try {
      return new RssResponse($this->generate());
    } catch(InvalidStateException | \InvalidArgumentException $e) {
      throw $e;
    }
  }
}
?>