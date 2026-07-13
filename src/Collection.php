<?php
declare(strict_types=1);

namespace Nexendrie\Rss;

use Nexendrie\Utils\Collection as BaseCollection;

/**
 * Collection
 *
 * @author Jakub Konečný
 * @extends BaseCollection<RssChannelItem>
 */
final class Collection extends BaseCollection
{
    public function __construct()
    {
        parent::__construct();
        $this->class = RssChannelItem::class;
    }
}
