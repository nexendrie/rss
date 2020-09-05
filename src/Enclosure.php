<?php
declare(strict_types=1);

namespace Nexendrie\Rss;

/**
 * Enclosure
 *
 * @author Jakub Konečný
 */
final class Enclosure implements IXmlConvertible {
  use \Nette\SmartObject;

  public string $url;
  public int $length;
  public string $type;

  public function __construct(string $url, int $length, string $type) {
    $this->url = $url;
    $this->length = $length;
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