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
 * @property string $language
 * @property string $copyright
 * @property string $managingEditor
 * @property string $webMaster
 * @property int|null $ttl
 * @property callable $dataSource
 * @property int $shortenDescription
 * @property string $dateTimeFormat
 * @property callable $lastBuildDate
 * @property callable|null $pubDate
 * @property string $generator
 * @property string $docs
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
  protected $language = "";
  /** @var string */
  protected $copyright = "";
  /** @var string */
  protected $managingEditor = "";
  /** @var string */
  protected $webMaster = "";
  /** @var int|null */
  protected $ttl = null;
  /** @var string */
  protected $dateTimeFormat = "r";
  /** @var callable|null */
  protected $dataSource = null;
  /** @var int */
  protected $shortenDescription = 150;
  /** @var callable */
  protected $lastBuildDate = "time";
  /** @var callable|null */
  protected $pubDate = null;
  /** @var string */
  protected $generator = "Nexendrie RSS";
  /** @var string */
  protected $docs = "http://www.rssboard.org/rss-specification";
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

  public function getLanguage(): string {
    return $this->language;
  }

  public function setLanguage(string $language): void {
    $this->language = $language;
  }

  public function getCopyright(): string {
    return $this->copyright;
  }

  public function setCopyright(string $copyright): void {
    $this->copyright = $copyright;
  }

  public function getManagingEditor(): string {
    return $this->managingEditor;
  }

  public function setManagingEditor(string $managingEditor): void {
    $this->managingEditor = $managingEditor;
  }

  public function getWebMaster(): string {
    return $this->webMaster;
  }

  public function setWebMaster(string $webMaster): void {
    $this->webMaster = $webMaster;
  }

  public function getTtl(): ?int {
    return $this->ttl;
  }

  public function setTtl(int $ttl): void {
    $this->ttl = $ttl;
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

  public function getPubDate(): ?callable {
    return $this->pubDate;
  }

  public function setPubDate(callable $pubDate): void {
    $this->pubDate = $pubDate;
  }

  public function getGenerator(): string {
    return $this->generator;
  }

  public function setGenerator(string $generator): void {
    $this->generator = $generator;
  }

  public function getDocs(): string {
    return $this->docs;
  }

  public function setDocs(string $docs): void {
    $this->docs = $docs;
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
      throw new \InvalidArgumentException("Callback for data source for RSS generator has to return an instance of  " . Collection::class . ".");
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

  protected function writeItemProperty(\SimpleXMLElement $element, RssChannelItem $item, string $property, callable $callback = null): void {
    if(isset($item->$property) AND $item->$property !== "") {
      $value = $item->$property;
      if(!is_null($callback)) {
        $value = $callback($value);
      }
      $element->addChild($property, $value);
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
    /** @var \SimpleXMLElement $channel */
    $channel = simplexml_load_file($this->template);
    $channel->channel->lastBuildDate[0][0] = date($this->dateTimeFormat, $lastBuildDate);
    if(isset($this->pubDate)) {
      $pubDate = call_user_func($this->pubDate);
      if(!is_int($pubDate)) {
        throw new \InvalidArgumentException("Callback for pub date for RSS generator has to return integer.");
      }
      $channel->channel->addChild("pubDate", date($this->dateTimeFormat, $pubDate));
    }
    $this->writeProperty($channel, "link");
    $this->writeProperty($channel, "title");
    $this->writeProperty($channel, "description");
    $this->writeProperty($channel, "language");
    $this->writeProperty($channel, "copyright");
    $this->writeProperty($channel, "managingEditor");
    $this->writeProperty($channel, "webMaster");
    $this->writeProperty($channel, "ttl");
    $this->writeProperty($channel, "generator");
    $this->writeProperty($channel, "docs");
    /** @var RssChannelItem $item */
    foreach($items as $item) {
      /** @var \SimpleXMLElement $i */
      $i = $channel->channel->addChild("item");
      $this->writeItemProperty($i, $item, "title");
      $this->writeItemProperty($i, $item, "link");
      $this->writeItemProperty($i, $item, "pubDate", function($value) {
        return date($this->dateTimeFormat, $value);
      });
      $this->writeItemProperty($i, $item, "description", [$this, "shortenDescription"]);
      $this->writeItemProperty($i, $item, "author");
      $this->writeItemProperty($i, $item, "comments");
      $this->writeItemProperty($i, $item, "guid");
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
    return new RssResponse($this->generate());
  }
}
?>