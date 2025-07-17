<?php
declare(strict_types=1);

namespace Nexendrie\Rss;

/**
 * GenericElement
 *
 * @author Jakub Konečný
 */
final class GenericElement implements XmlConvertible {
  use \Nette\SmartObject;

  public function __construct(public string $name, public mixed $value) {
  }

  public function appendToXml(\SimpleXMLElement &$parent): void {
    $value = (string) $this->value;
    if($value === "") {
      return;
    }
    $parent->{$this->name} = $value;
  }
}
?>