<?php
declare(strict_types=1);

namespace Nexendrie\Rss;

/**
 * GenericElement
 *
 * @author Jakub Konečný
 * @internal
 * @property-read string $name
 * @property mixed $value
 */
final class GenericElement implements IXmlConvertible {
  use \Nette\SmartObject;

  /**
   * @var string
   */
  private $name;

  /**
   * @var mixed
   */
  private $value;

  /**
   * @param mixed $value
   */
  public function __construct(string $name, $value) {
    $this->name = $name;
    $this->value = $value;
  }

  public function getName(): string {
    return $this->name;
  }

  /**
   * @return mixed
   */
  public function getValue() {
    return $this->value;
  }

  /**
   * @param mixed $value
   */
  public function setValue($value): void {
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