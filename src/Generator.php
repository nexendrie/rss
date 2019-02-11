<?php
declare(strict_types=1);

namespace Nexendrie\Rss;

use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\OptionsResolver\Options;
use Nette\Utils\Arrays;
use Nexendrie\Utils\Numbers;

/**
 * RSS Channel Generator
 *
 * @author Jakub Konečný
 * @property callable $dataSource
 * @property int $shortenDescription
 * @property string $dateTimeFormat
 * @property string $generator
 * @property string $docs
 * @property string $template
 * @method void onBeforeGenerate(Generator $generator, array $info)
 * @method void onAddItem(Generator $generator, \SimpleXMLElement $channel, RssChannelItem $itemDefinition, \SimpleXMLElement $item)
 * @method void onAfterGenerate(Generator $generator, array $info)
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
    if(!is_file($template) OR !is_readable($template)) {
      throw new \RuntimeException("File $template does not exist or is not readable.");
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
  
  protected function writeProperty(\SimpleXMLElement &$channel, array $info, string $property): void {
    $value = Arrays::get($info, $property, "");
    if($value === "") {
      return;
    }
    switch($property) {
      case "skipDays":
        $element = $channel->channel->addChild("skipDays");
        array_walk($value, function(string $value) use($element) {
          $element->addChild("day", $value);
        });
        break;
      case "skipHours":
        $element = $channel->channel->addChild("skipHours");
        array_walk($value, function(string $value) use($element) {
          $element->addChild("hour", $value);
        });
        break;
      default:
        $channel->channel->{$property} = $value;
        break;
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
      "language", "copyright", "managingEditor", "webMaster", "ttl",  "pubDate", "rating", "categories", "skipDays",
      "skipHours",
    ]);
    $resolver->setAllowedTypes("language", "string");
    $resolver->setAllowedTypes("copyright", "string");
    $resolver->setAllowedTypes("managingEditor", "string");
    $resolver->setAllowedTypes("webMaster", "string");
    $resolver->setAllowedTypes("ttl", "int");
    $resolver->setAllowedValues("ttl", function(int $value) {
      return ($value >= 0);
    });
    $resolver->setAllowedTypes("pubDate", "callable");
    $resolver->setAllowedTypes("rating", "string");
    $resolver->setAllowedTypes("categories", Category::class . "[]");
    $resolver->setAllowedTypes("skipDays", "string[]");
    $resolver->setAllowedValues("skipDays", function(array $value) {
      $allowedValues = ["Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday", "Sunday", ];
      return Arrays::every($value, function(string $value) use($allowedValues) {
        return in_array($value, $allowedValues, true);
      });
    });
    $resolver->setNormalizer("skipDays", function(Options $options, array $value) {
      return array_unique($value);
    });
    $resolver->setAllowedTypes("skipHours", "int[]");
    $resolver->setAllowedValues("skipHours", function(array $value) {
      return Arrays::every($value, function(int $value) {
        return Numbers::isInRange($value, 0, 23);
      });
    });
    $resolver->setNormalizer("skipHours", function(Options $options, array $value) {
      array_walk($value, function(int &$value) {
        if($value < 10) {
          $value = "0" . (string) $value;
        }
        $value = (string) $value;
      });
      return array_unique($value);
    });
  }
  
  /**
   * @throws InvalidStateException
   * @throws \InvalidArgumentException
   */
  public function generate(array $info): string {
    $this->onBeforeGenerate($this, $info);
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
    $channel->channel->lastBuildDate = date($this->dateTimeFormat, $lastBuildDate);
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
    $this->writeProperty($channel, $info, "skipDays");
    $this->writeProperty($channel, $info, "skipHours");
    if($this->generator !== "") {
      $channel->channel->generator = $this->generator;
    }
    if($this->docs !== "") {
      $channel->channel->docs = $this->docs;
    }
    $this->writeProperty($channel, $info, "rating");
    $categories =  Arrays::get($info, "categories", []);
    array_walk($categories, function(Category $value) use($channel) {
      $value->appendToXml($channel->channel);
    });
    /** @var RssChannelItem $item */
    foreach($items as $item) {
      /** @var \SimpleXMLElement $i */
      $i = $channel->channel->addChild("item");
      $item->toXml($i, $this);
      $this->onAddItem($this, $channel, $item, $i);
    }
    $this->onAfterGenerate($this, $info);
    $dom = new \DOMDocument();
    $dom->preserveWhiteSpace = false;
    $dom->formatOutput = true;
    $dom->loadXML($channel->asXML());
    return $dom->saveXML();
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