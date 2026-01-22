<?php
declare(strict_types=1);

namespace Nexendrie\Rss\Events;

use Nexendrie\Rss\Generator;

final readonly class ChannelBeforeGenerate
{
    public function __construct(public Generator $generator, public array $info)
    {
    }
}
