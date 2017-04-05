<?php
declare(strict_types=1);

namespace Nexendrie\Rss;

use Tester\Assert,
    Nexendrie\Rss\RssChannelItem as Item;

require __DIR__ . "/../../bootstrap.php";

class CollectionTest extends \Tester\TestCase {
  /** @var  Collection */
  protected $col;
  /** @var  string */
  protected $pubDate;
  
  /**
   * @return void
   */
  function setUp() {
    $this->col = new Collection;
    $this->pubDate = date("Y-m-d H:i:s");
  }
  
  /**
   * @return void
   */
  function testCount() {
    Assert::same(0, count($this->col));
    $this->col[] = new Item("Item 1", "Item 1 description", "", $this->pubDate);
    Assert::same(1, count($this->col));
  }
  
  /**
   * @return void
   */
  function testGetIterator() {
    for($i = 1; $i <= 5; $i++) {
      $this->col[] = new Item("Item 1", "Item 1 description", "", $this->pubDate);
    }
    /** @var Item $item */
    foreach($this->col as $item) {
      Assert::same("Item 1", $item->title);
    }
  }
  
  /**
   * @return void
   */
  function testOffsetExists() {
    Assert::false(isset($this->col[0]));
    $this->col[] = new Item("Item 1", "Item 1 description", "", $this->pubDate);
    Assert::true(isset($this->col[0]));
  }
  
  /**
   * @return void
   */
  function testOffsetGet() {
    $this->col[] = new Item("Item 1", "Item 1 description", "", $this->pubDate);
    $item = $this->col[0];
    Assert::type(Item::class, $item);
    Assert::exception(function() {
      $item = $this->col[1];
    }, \OutOfRangeException::class);
  }
  
  function testOffsetSet() {
    $this->col[] = new Item("Item 1", "Item 1 description", "", $this->pubDate);
    $this->col[0] = new Item("Item 2", "Item 1 description", "", $this->pubDate);
    Assert::same("Item 2", $this->col[0]->title);
    Assert::exception(function() {
      $this->col[] = new \stdClass;
    }, \InvalidArgumentException::class);
    Assert::exception(function() {
      $this->col[-1] = new Item("Item 1", "Item 1 description", "", $this->pubDate);
    }, \OutOfRangeException::class);
  }
  
  /**
   * @return void
   */
  function testOffsetUnset() {
    $this->col[] = new Item("Item 1", "Item 1 description", "", $this->pubDate);
    unset($this->col[0]);
    Assert::false(isset($this->col[0]));
    Assert::exception(function() {
      unset($this->col[0]);
    }, \OutOfRangeException::class);
  }
}

$test = new CollectionTest;
$test->run();
?>