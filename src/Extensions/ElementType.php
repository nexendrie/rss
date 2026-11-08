<?php
declare(strict_types=1);

namespace Nexendrie\Rss\Extensions;

use Closure;

interface ElementType
{
    public function getName(): string;
    public function getSimpleType(): SimpleElementType;

    /**
     * @return Closure(mixed $value): bool
     */
    public function getValidator(): Closure;
}
