<?php
declare(strict_types=1);

namespace Nexendrie\Rss;

/**
 * SkipHoursCollection
 *
 * @author Jakub Konečný
 * @internal
 */
final class SkipHoursCollection implements IXmlConvertible {
  private array $hours = [];

  /**
   * @param int[] $hours
   */
  public function __construct(array $hours) {
    array_walk($hours, function(int &$value): void {
      $value = (string) $value;
    });
    $hours = array_unique($hours);
    $this->hours = $hours;
  }

  public function appendToXml(\SimpleXMLElement &$parent): void {
    $element = $parent->addChild("skipHours");
    array_walk($this->hours, function(string $value) use ($element): void {
      $element->addChild("hour", $value);
    });
  }
}
?>