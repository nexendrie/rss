<?php
declare(strict_types=1);

namespace Nexendrie\Rss\Extensions;

use Closure;
use Nexendrie\Rss\XmlConvertible;

/**
 * @internal
 */
interface NormalizedElementType extends ElementType
{
    /**
     * @return Closure(string $name, mixed $value, ?string $namespaceUri): (string|XmlConvertible)
     */
    public function getNormalizer(): Closure;
}
