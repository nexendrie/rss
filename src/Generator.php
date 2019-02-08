<?php
declare(strict_types=1);

namespace Nexendrie\Rss;

use Symfony\Component\OptionsResolver\OptionsResolver;
use Nette\Utils\Arrays;

/**
 * RSS Channel Generator
 *
 * @author Jakub Konečný
 * @property callable $dataSource
 * @property int $shortenDescription
 * @property string $dateTimeFormat
 * @property string $template
 * @method void onBeforeGenerate(Generator $generator)
 * @method void onAddItem(Generator $generator, \SimpleXMLElement $channel, RssChannelItem $itemDefinition, \SimpleXMLElement $item)
 * @method void onAfterGenerate(Generator $generator)
 */
final class Generator {
  use \Nette\SmartObject;

  /** @var string */
  protected $dateTimeFormat = "r";
  /** @var callable|null */
  protected $dataSource = null;
  /** @var int */
  protected $shortenDescription = 150;
  /** @var string */
  protected $template = __DIR__ . "/template.xml";
  /** @var callable[] */
  public $onBeforeGenerate = [];
  /** @var callable[] */
  public $onAddItem = [];
  /** @var callable[] */
  public $onAfterGenerate = [];

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
  
  protected function writeProperty(\SimpleXMLElement &$channel, array $info, string $property): void {
    $value = Arrays::get($info, $property, "");
    if($value !== "") {
      $channel->channel->{$property}[0][0] = $value;
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

  protected function configureOptions(OptionsResolver $resolver): void {
    $resolver->setRequired(["title", "description", "link", "lastBuildDate", ]);
    $resolver->setAllowedTypes("title", "string");
    $resolver->setAllowedTypes("description", "string");
    $resolver->setAllowedTypes("link", "string");
    $resolver->setAllowedTypes("lastBuildDate", "callable");
    $resolver->setDefault("lastBuildDate", "time");
    $resolver->setDefined([
      "language", "copyright", "managingEditor", "webMaster", "ttl", "generator", "docs", "pubDate", "rating",
    ]);
    $resolver->setAllowedTypes("language", "string");
    $resolver->setAllowedTypes("copyright", "string");
    $resolver->setAllowedTypes("managingEditor", "string");
    $resolver->setAllowedTypes("webMaster", "string");
    $resolver->setAllowedTypes("ttl", "int");
    $resolver->setAllowedValues("ttl", function(int $value) {
      return ($value >= 0);
    });
    $resolver->setAllowedTypes("generator", "string");
    $resolver->setDefault("generator", "Nexendrie RSS");
    $resolver->setAllowedTypes("docs", "string");
    $resolver->setDefault("docs", "http://www.rssboard.org/rss-specification");
    $resolver->setAllowedTypes("pubDate", "callable");
    $resolver->setAllowedTypes("rating", "string");
  }
  
  /**
   * @throws InvalidStateException
   * @throws \InvalidArgumentException
   */
  public function generate(array $info): string {
    $this->onBeforeGenerate($this);
    $items = $this->getData();
    $resolver = new OptionsResolver();
    $this->configureOptions($resolver);
    $info = $resolver->resolve($info);
    $lastBuildDate = call_user_func($info["lastBuildDate"]);
    if(!is_int($lastBuildDate)) {
      throw new \InvalidArgumentException("Callback for last build date for RSS generator has to return integer.");
    }
    /** @var \SimpleXMLElement $channel */
    $channel = simplexml_load_file($this->template);
    $channel->channel->lastBuildDate[0][0] = date($this->dateTimeFormat, $lastBuildDate);
    if(isset($info["pubDate"])) {
      $pubDate = call_user_func($info["pubDate"]);
      if(!is_int($pubDate)) {
        throw new \InvalidArgumentException("Callback for pub date for RSS generator has to return integer.");
      }
      $channel->channel->addChild("pubDate", date($this->dateTimeFormat, $pubDate));
    }
    $this->writeProperty($channel, $info, "link");
    $this->writeProperty($channel, $info, "title");
    $this->writeProperty($channel, $info, "description");
    $this->writeProperty($channel, $info, "language");
    $this->writeProperty($channel, $info, "copyright");
    $this->writeProperty($channel, $info, "managingEditor");
    $this->writeProperty($channel, $info, "webMaster");
    $this->writeProperty($channel, $info, "ttl");
    $this->writeProperty($channel, $info, "generator");
    $this->writeProperty($channel, $info, "docs");
    $this->writeProperty($channel, $info, "rating");
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
  public function response(array $info): RssResponse {
    return new RssResponse($this->generate($info));
  }
}
?>