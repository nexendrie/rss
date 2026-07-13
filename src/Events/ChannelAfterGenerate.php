<?php
declare(strict_types=1);

namespace Nexendrie\Rss\Events;

use Nexendrie\Rss\Generator;

final readonly class ChannelAfterGenerate
{
    /**
     * @param array<string, mixed> $info
     */
    public function __construct(public Generator $generator, public array $info)
    {
    }
}
