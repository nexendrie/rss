<?php
declare(strict_types=1);

namespace Nexendrie\Rss;

/**
 * Category
 *
 * @author Jakub Konečný
 */
final class Category implements IXmlConvertible {
  use \Nette\SmartObject;

  public string $identifier;
  public string $domain;

  public function __construct(string $identifier, string $domain = "") {
    $this->identifier = $identifier;
    $this->domain = $domain;
  }

  public function appendToXml(\SimpleXMLElement &$parent): void {
    $categoryElement = $parent->addChild("category", $this->identifier);
    if($this->domain !== "") {
      $categoryElement->addAttribute("domain", $this->domain);
    }
  }
}
?>