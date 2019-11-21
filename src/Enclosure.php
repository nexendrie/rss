<?php
declare(strict_types=1);

namespace Nexendrie\Rss;

/**
 * Enclosure
 *
 * @author Jakub Konečný
 * @property string $url
 * @property int $length
 * @property string $type
 */
final class Enclosure implements IXmlConvertible {
  use \Nette\SmartObject;

  /** @var string */
  protected $url;
  /** @var int */
  protected $length;
  /** @var string */
  protected $type;

  public function __construct(string $url, int $length, string $type) {
    $this->url = $url;
    $this->length = $length;
    $this->type = $type;
  }

  protected function getUrl(): string {
    return $this->url;
  }

  protected function setUrl(string $url): void {
    $this->url = $url;
  }

  protected function getLength(): int {
    return $this->length;
  }

  protected function setLength(int $length): void {
    $this->length = $length;
  }

  protected function getType(): string {
    return $this->type;
  }

  protected function setType(string $type): void {
    $this->type = $type;
  }

  public function appendToXml(\SimpleXMLElement &$parent): void {
    $enclosureElement = $parent->addChild("enclosure");
    $enclosureElement->addAttribute("url", $this->url);
    $enclosureElement->addAttribute("length", (string) $this->length);
    $enclosureElement->addAttribute("type", $this->type);
  }
}
?>