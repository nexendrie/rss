<?php
declare(strict_types=1);

namespace Nexendrie\Rss\Events;

use Nexendrie\Rss\Generator;
use Nexendrie\Rss\RssChannelItem;

final readonly class ItemAdded
{
    public function __construct(
        public Generator $generator,
        public \SimpleXMLElement $channel,
        public RssChannelItem $itemDefinition,
        public \SimpleXMLElement $item
    ) {
    }
}
