<?php
declare(strict_types=1);

namespace Nexendrie\Rss;

/**
 * GenericElement
 *
 * @author Jakub Konečný
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

  protected function getName(): string {
    return $this->name;
  }

  /**
   * @return mixed
   */
  protected function getValue() {
    return $this->value;
  }

  /**
   * @param mixed $value
   */
  protected function setValue($value): void {
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