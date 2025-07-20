<?php
declare(strict_types=1);

namespace Nexendrie\Rss;

/**
 * Source
 *
 * @author Jakub Konečný
 */
final class Source extends \stdClass implements XmlConvertible {
  public function __construct(public string $url = "", public string $title = "") {
  }

  public function appendToXml(\SimpleXMLElement &$parent): void {
    if($this->url !== "") {
      $element = $parent->addChild("source", $this->title);
      $element->addAttribute("url", $this->url);
    }
  }
}
?>