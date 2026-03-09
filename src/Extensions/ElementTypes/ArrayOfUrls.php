<?php
declare(strict_types=1);

namespace Nexendrie\Rss\Extensions\ElementTypes;

use Closure;
use Nexendrie\Rss\Extensions\ElementType;
use Nexendrie\Rss\Extensions\SimpleElementType;

final class ArrayOfUrls implements ElementType
{
    public function getName(): string
    {
        return "url[]";
    }

    public function getSimpleType(): SimpleElementType
    {
        return SimpleElementType::ArrayOfStrings;
    }

    public function getValidator(): Closure
    {
        return static fn(array $value): bool =>
        array_all($value, static fn (string $value): bool => (new Url())->getValidator()($value));
    }
}
