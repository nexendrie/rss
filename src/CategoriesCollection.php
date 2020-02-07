<?php
declare(strict_types=1);

namespace Nexendrie\Rss;

use Nexendrie\Utils\Collection as BaseCollection;

/**
 * CategoriesCollection
 *
 * @author Jakub Konečný
 */
final class CategoriesCollection extends BaseCollection implements IXmlConvertible {
  /** @var string */
  protected $class = Category::class;

  public function appendToXml(\SimpleXMLElement &$parent): void {
    array_walk($this->items, function(Category $value) use ($parent): void {
      $value->appendToXml($parent);
    });
  }
}
?>