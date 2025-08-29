<?php
declare(strict_types=1);

namespace Nexendrie\Rss\Events;

use Nexendrie\Rss\Generator;

final class ChannelAfterGenerate
{
    public function __construct(public readonly Generator $generator, public readonly array $info)
    {
    }
}
