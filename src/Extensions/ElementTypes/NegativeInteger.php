<?php
declare(strict_types=1);

namespace Nexendrie\Rss\Extensions\ElementTypes;

use Closure;
use Nexendrie\Rss\Extensions\ElementType;
use Nexendrie\Rss\Extensions\SimpleElementType;

final class NegativeInteger implements ElementType
{
    public function getName(): string
    {
        return "negative-int";
    }

    public function getSimpleType(): SimpleElementType
    {
        return SimpleElementType::Integer;
    }

    public function getValidator(): Closure
    {
        return static fn(int $value): bool => ($value < 0);
    }
}
