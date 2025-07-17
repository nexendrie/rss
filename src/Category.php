<?php
declare(strict_types=1);

namespace Nexendrie\Rss;

/**
 * Category
 *
 * @author Jakub Konečný
 */
final class Category implements XmlConvertible {
  use \Nette\SmartObject;

  public function __construct(public string $identifier, public string $domain = "") {
  }

  public function appendToXml(\SimpleXMLElement &$parent): void {
    $categoryElement = $parent->addChild("category", $this->identifier);
    if($this->domain !== "") {
      $categoryElement->addAttribute("domain", $this->domain);
    }
  }
}
?>