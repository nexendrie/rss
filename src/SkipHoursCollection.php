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
  /** @var string[] */
  protected $hours = [];

  /**
   * @param int[] $hours
   */
  public function __construct(array $hours) {
    array_walk($hours, function(int &$value) {
      $value = (string) $value;
    });
    /** @var string[] $hours */
    $hours = array_unique($hours);
    $this->hours = $hours;
  }

  public function appendToXml(\SimpleXMLElement &$parent): void {
    $element = $parent->addChild("skipHours");
    array_walk($this->hours, function(string $value) use($element) {
      $element->addChild("hour", $value);
    });
  }
}
?>