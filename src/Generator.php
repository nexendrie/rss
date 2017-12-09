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
 * @method void onBeforeGenerate(Generator $generator)
 * @method void onAddItem(Generator $generator, \SimpleXMLElement $channel, RssChannelItem $itemDefinition, \SimpleXMLElement $item)
 * @method void onAfterGenerate(Generator $generator)
 */
final class Generator {
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
  /** @var callable[] */
  public $onBeforeGenerate = [];
  /** @var callable[] */
  public $onAddItem = [];
  /** @var callable[] */
  public $onAfterGenerate = [];
  
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
  
  public function setDataSource(callable $dataSource) {
    $this->dataSource = $dataSource;
  }
  
  public function getShortenDescription(): int {
    return $this->shortenDescription;
  }
  
  public function setShortenDescription(int $value) {
    $this->shortenDescription = $value;
  }
  
  public function getDateTimeFormat(): string {
    return $this->dateTimeFormat;
  }
  
  public function setDateTimeFormat(string $format) {
    $this->dateTimeFormat = $format;
  }
  
  public function getLastBuildDate() {
    return $this->lastBuildDate;
  }
  
  public function setLastBuildDate(callable $lastBuildDate) {
    $this->lastBuildDate = $lastBuildDate;
  }
  
  /**
   * @throws InvalidStateException
   * @throws \InvalidArgumentException
   */
  protected function getData(): Collection {
    if(is_null($this->dataSource)) {
      throw new InvalidStateException("Data source for RSS generator is not set.");
    }
    $items = call_user_func($this->dataSource);
    if(!$items instanceof Collection) {
      throw new \InvalidArgumentException("Callback for data source for RSS generator has to return " . Collection::class . ".");
    }
    return $items;
  }
  
  protected function shortenDescription(string $description): string {
    if($this->shortenDescription < 1) {
      return $description;
    }
    $originalDescription = $description;
    $description = substr($description, 0, $this->shortenDescription);
    if($description !== $originalDescription) {
      $description .= "...";
    }
    return $description;
  }
  
  protected function writeProperty(\SimpleXMLElement &$channel, string $property): void {
    if(isset($this->$property) AND $this->$property !== "") {
      $channel->channel->{$property}[0][0] = $this->$property;
    }
  }
  
  /**
   * @throws InvalidStateException
   * @throws \InvalidArgumentException
   */
  public function generate(): \SimpleXMLElement {
    $this->onBeforeGenerate($this);
    $items = $this->getData();
    $lastBuildDate = call_user_func($this->lastBuildDate);
    if(!is_int($lastBuildDate)) {
      throw new \InvalidArgumentException("Callback for last build date for RSS generator has to return integer.");
    }
    $channel = simplexml_load_file(__DIR__ . "/template.xml");
    $channel->channel->lastBuildDate[0][0] = date($this->dateTimeFormat, $lastBuildDate);
    $this->writeProperty($channel, "link");
    $this->writeProperty($channel, "title");
    $this->writeProperty($channel, "description");
    /** @var RssChannelItem $item */
    foreach($items as $item) {
      /** @var \SimpleXMLElement $i */
      $i = $channel->channel->addChild("item");
      $i->addChild("title", $item->title);
      $i->addChild("link", $item->link);
      $i->addChild("pubDate", $item->pubDate);
      $i->addChild("description", $this->shortenDescription($item->description));
      $this->onAddItem($this, $channel, $item, $i);
    }
    $this->onAfterGenerate($this);
    return $channel;
  }
  
  /**
   * @throws InvalidStateException
   * @throws \InvalidArgumentException
   */
  public function response(): RssResponse {
    try {
      return new RssResponse($this->generate());
    } catch(InvalidStateException | \InvalidArgumentException $e) {
      throw $e;
    }
  }
}
?>