<?php
declare(strict_types=1);

namespace Nexendrie\Rss;

/**
 * Collection
 *
 * @author Jakub Konečný
 */
class Collection implements \ArrayAccess, \Countable, \IteratorAggregate {
  /** @var RssChannelItem[] Items in the collection */
  protected $items = [];
  /** @var string Type of items in the collection */
  protected $class = RssChannelItem::class;
  
  /**
   * @return int
   */
  function count(): int {
    return count($this->items);
  }
  
  /**
   * @return \ArrayIterator
   */
  function getIterator(): \ArrayIterator {
    return new \ArrayIterator($this->items);
  }
  
  /**
   * @param int $index
   * @return bool
   */
  function offsetExists($index): bool {
    return $index >= 0 AND $index < count($this->items);
  }
  
  /**
   * @param int $index
   * @return RssChannelItem
   * @throws \OutOfRangeException
   */
  function offsetGet($index): RssChannelItem {
    if($index < 0 OR $index >= count($this->items)) {
      throw new \OutOfRangeException("Offset invalid or out of range.");
    }
    return $this->items[$index];
  }
  
  /**
   * @param int $index
   * @param RssChannelItem $item
   * @return void
   * @throws \OutOfRangeException
   * @throws \InvalidArgumentException
   */
  function offsetSet($index, $item): void {
    if(!$item instanceof $this->class) {
      throw new \InvalidArgumentException("Argument must be of $this->class type.");
    }
    if($index === NULL) {
      $this->items[] = & $item;
    } elseif($index < 0 OR $index >= count($this->items)) {
      throw new \OutOfRangeException("Offset invalid or out of range.");
    } else {
      $this->items[$index] = & $item;
    }
  }
  
  /**
   * @param int $index
   * @return void
   * @throws \OutOfRangeException
   */
  function offsetUnset($index): void {
    if($index < 0 OR $index >= count($this->items)) {
      throw new \OutOfRangeException("Offset invalid or out of range.");
    }
    array_splice($this->items, $index, 1);
  }
}
?>