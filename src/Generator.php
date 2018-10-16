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
 * @property string $template
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
  protected $dataSource = null;
  /** @var int */
  protected $shortenDescription = 150;
  /** @var callable */
  protected $lastBuildDate = "time";
  /** @var string */
  protected $template = __DIR__ . "/template.xml";
  /** @var callable[] */
  public $onBeforeGenerate = [];
  /** @var callable[] */
  public $onAddItem = [];
  /** @var callable[] */
  public $onAfterGenerate = [];
  
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
  
  public function setDataSource(callable $dataSource): void {
    $this->dataSource = $dataSource;
  }
  
  public function getShortenDescription(): int {
    return $this->shortenDescription;
  }
  
  public function setShortenDescription(int $value): void {
    $this->shortenDescription = $value;
  }
  
  public function getDateTimeFormat(): string {
    return $this->dateTimeFormat;
  }
  
  public function setDateTimeFormat(string $format): void {
    $this->dateTimeFormat = $format;
  }
  
  public function getLastBuildDate(): callable {
    return $this->lastBuildDate;
  }
  
  public function setLastBuildDate(callable $lastBuildDate): void {
    $this->lastBuildDate = $lastBuildDate;
  }
  
  public function getTemplate(): string {
    return $this->template;
  }
  
  /**
   * @throws \RuntimeException
   */
  public function setTemplate(string $template): void {
    if(!is_file($template)) {
      throw new \RuntimeException("File $template does not exist.");
    }
    $this->template = $template;
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
  public function generate(): string {
    $this->onBeforeGenerate($this);
    $items = $this->getData();
    $lastBuildDate = call_user_func($this->lastBuildDate);
    if(!is_int($lastBuildDate)) {
      throw new \InvalidArgumentException("Callback for last build date for RSS generator has to return integer.");
    }
    $channel = simplexml_load_file($this->template);
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
    return $channel->asXML();
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