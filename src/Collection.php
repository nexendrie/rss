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
  protected string $class = RssChannelItem::class;
}
?>