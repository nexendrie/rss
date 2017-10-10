<?php
declare(strict_types=1);

namespace Nexendrie\Rss;

use Nexendrie\Utils\Collection as BaseCollection;

/**
 * Collection
 *
 * @author Jakub Konečný
 */
final class Collection extends BaseCollection {
  /** @var string Type of items in the collection */
  protected $class = RssChannelItem::class;
}
?>