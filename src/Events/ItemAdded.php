<?php
declare(strict_types=1);

namespace Nexendrie\Rss\Events;

use Nexendrie\Rss\Generator;
use Nexendrie\Rss\RssChannelItem;

final class ItemAdded
{
    public function __construct(public readonly Generator $generator, public readonly \SimpleXMLElement $channel, public readonly RssChannelItem $itemDefinition, public readonly \SimpleXMLElement $item)
    {
    }
}
