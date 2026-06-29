<?php
declare(strict_types=1);

namespace Nexendrie\Rss\Extensions\ElementTypes;

use Closure;
use Nexendrie\Rss\Extensions\ElementType;
use Nexendrie\Rss\Extensions\SimpleElementType;

/**
 * @see https://www.w3.org/TR/NOTE-datetime
 */
class DateTimeString implements ElementType
{
    public function getName(): string
    {
        return "datetime-string";
    }

    public function getSimpleType(): SimpleElementType
    {
        return SimpleElementType::String;
    }

    public function getValidator(): Closure
    {
        return static fn(string $value): bool => preg_match('/^\d{4}(?:-\d{2}(?:-\d{2}(?:T\d{2}:\d{2}(?::\d{2})?(?:Z|[+-]\d{2}:\d{2})?)?)?)?$/', $value) === 1;
    }
}
