<?php
declare(strict_types=1);

namespace Nexendrie\Rss;

/**
 * GenericElement
 *
 * @author Jakub Konečný
 */
final class GenericElement implements IXmlConvertible {
  use \Nette\SmartObject;

  public string $name;

  /**
   * @var mixed
   */
  public $value;

  /**
   * @param mixed $value
   */
  public function __construct(string $name, $value) {
    $this->name = $name;
    $this->value = $value;
  }

  public function appendToXml(\SimpleXMLElement &$parent): void {
    if(empty($this->value)) {
      return;
    }
    $parent->{$this->name} = $this->value;
  }
}
?>