<?php
declare(strict_types=1);

namespace Nexendrie\Rss;

/**
 * Category
 *
 * @author Jakub Konečný
 * @property string $identifier
 * @property string $domain
 */
final class Category implements IXmlConvertible {
  use \Nette\SmartObject;

  /** @var string */
  protected $identifier;
  /** @var string */
  protected $domain;

  public function __construct(string $identifier, string $domain = "") {
    $this->identifier = $identifier;
    $this->domain = $domain;
  }

  public function getIdentifier(): string {
    return $this->identifier;
  }

  public function setIdentifier(string $identifier): void {
    $this->identifier = $identifier;
  }

  public function getDomain(): string {
    return $this->domain;
  }

  public function setDomain(string $domain): void {
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