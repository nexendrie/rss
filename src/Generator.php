<?php
declare(strict_types=1);

namespace Nexendrie\Rss;

use Nexendrie\Rss\Extensions\RssCore;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Nette\Utils\Arrays;

/**
 * RSS Channel Generator
 *
 * @author Jakub Konečný
 * @property-write callable $dataSource
 * @property int $shortenDescription
 * @property string $dateTimeFormat
 * @property string $generator
 * @property string $docs
 * @property string $template
 * @property \Nexendrie\Utils\Collection|IRssExtension[] $extensions
 * @method void onBeforeGenerate(Generator $generator, array $info)
 * @method void onAddItem(Generator $generator, \SimpleXMLElement $channel, RssChannelItem $itemDefinition, \SimpleXMLElement $item)
 * @method void onAfterGenerate(Generator $generator, array $info)
 */
final class Generator {
  use \Nette\SmartObject;

  private const NAMESPACE_ATTRIBUTE_HACK = "__extension_namespace__";

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
  /** @var \Nexendrie\Utils\Collection|IRssExtension[] */
  protected $extensions;
  /** @var callable[] */
  public $onBeforeGenerate = [];
  /** @var callable[] */
  public $onAddItem = [];
  /** @var callable[] */
  public $onAfterGenerate = [];

  public function __construct() {
    $this->extensions = new class extends \Nexendrie\Utils\Collection {
      /** @var string */
      protected $class = IRssExtension::class;
    };
    $this->extensions[] = new RssCore();
  }

  protected function setDataSource(callable $dataSource): void {
    $this->dataSource = $dataSource;
  }

  protected function getShortenDescription(): int {
    return $this->shortenDescription;
  }

  protected function setShortenDescription(int $value): void {
    $this->shortenDescription = $value;
  }

  protected function getDateTimeFormat(): string {
    return $this->dateTimeFormat;
  }

  protected function setDateTimeFormat(string $format): void {
    $this->dateTimeFormat = $format;
  }

  protected function getGenerator(): string {
    return $this->generator;
  }

  protected function setGenerator(string $generator): void {
    $this->generator = $generator;
  }

  protected function getDocs(): string {
    return $this->docs;
  }

  protected function setDocs(string $docs): void {
    $this->docs = $docs;
  }

  protected function getTemplate(): string {
    return $this->template;
  }
  
  /**
   * @throws \RuntimeException
   */
  protected function setTemplate(string $template): void {
    if(!is_file($template) || !is_readable($template)) {
      throw new \RuntimeException("File $template does not exist or is not readable.");
    }
    $this->template = $template;
  }

  public function getExtensions(): \Nexendrie\Utils\Collection {
    return $this->extensions;
  }
  
  /**
   * @throws InvalidStateException
   * @throws \InvalidArgumentException
   */
  protected function getData(): Collection {
    if($this->dataSource === null) {
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
    if(!$value instanceof IXmlConvertible) {
      $value = new GenericElement($property, $value);
    }
    $value->appendToXml($channel->channel);
  }
  
  /**
   * @throws InvalidStateException
   * @throws \InvalidArgumentException
   */
  public function generate(array $info): string {
    $this->onBeforeGenerate($this, $info);
    $items = $this->getData();
    $resolver = new OptionsResolver();
    foreach($this->extensions as $extension) {
      $extension->configureChannelOptions($resolver, $this);
    }
    $info = $resolver->resolve($info);
    /** @var \SimpleXMLElement $channel */
    $channel = simplexml_load_file($this->template);
    foreach($this->extensions as $extension) {
      if($extension->getName() !== "" && $extension->getNamespace() !== "") {
        $channel->addAttribute(static::NAMESPACE_ATTRIBUTE_HACK . $extension->getName(), $extension->getNamespace());
      }
    }
    $properties = $resolver->getDefinedOptions();
    foreach($properties as $property) {
      $this->writeProperty($channel, $info, $property);
    }
    if($this->generator !== "") {
      $channel->channel->generator = $this->generator;
    }
    if($this->docs !== "") {
      $channel->channel->docs = $this->docs;
    }
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
    $xml = $channel->asXML();
    $xml = str_replace(static::NAMESPACE_ATTRIBUTE_HACK, "xmlns:", $xml);
    $dom->loadXML($xml);
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