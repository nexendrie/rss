<?php
declare(strict_types=1);

namespace Nexendrie\Rss\Extensions\ElementTypes;

use Closure;
use Nexendrie\Rss\Extensions\ElementType;
use Nexendrie\Rss\Extensions\SimpleElementType;

final class EmailAddress implements ElementType
{
    public function getName(): string
    {
        return "email";
    }

    public function getSimpleType(): SimpleElementType
    {
        return SimpleElementType::String;
    }

    public function getValidator(): Closure
    {
        return static fn(string $value): bool => preg_match('/^(?:[a-zA-Z0-9\.\+-_]+)@(?:[a-zA-Z\.-]+)\.[a-z]{2,}(?: \([a-zA-Z0-9 \p{L}\p{M}]+\))?$/u', $value) === 1;
    }
}
